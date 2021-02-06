<?php

namespace App\Http\Controllers\API\Post;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Post\AddWatermarkRequest;
use App\Model\AccountManager\AccountManager;
use App\Model\SocialNetwork;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ImageToken;

class WatermarkController extends ApiController
{
    public function addWatermark(AddWatermarkRequest $request)
    {
        $filetype = null;
        $file = null;
        if ($request->file('watermark_mask')) {
            $file = $request->file('watermark_mask')->getClientOriginalName();
            $filetype = pathinfo($file, PATHINFO_EXTENSION);
        }

        $social_network_id = $request->social_network_id;
        $social_network = SocialNetwork::find($social_network_id);
        if (!$social_network) {
            return $this->fail(trans('global.not_found'));
        }

        if ($filetype == "png") {
            $bucket_path = env('AWS_BUCKET_DIR') . '/' . Auth::id() . '/watermarks';
            $path = Storage::disk('s3')->put($bucket_path, $request->file('watermark_mask'), 'private');
            if (!$path) {
                return $this->fail("Image upload error");
            } else {
                $details = [
                    'watermark_mask' => $path,
                    'watermark_size' => $request->watermark_size,
                    'watermark_opacity' => $request->watermark_opacity,
                    'watermark_position' => $request->watermark_position
                ];
                AccountManager::where('user_id', Auth::id())
                    ->where('social_network_id', $request->social_network_id)
                    ->update(['watermark_details' => $details]);

            }
        } else if (!$file) {
            $old_details = AccountManager::select('watermark_details')
                ->where('user_id', Auth::id())
                ->where('social_network_id', $request->social_network_id)->first();

            error_log($old_details);

            $watermark_mask = json_decode($old_details->watermark_details)->watermark_mask;

            error_log($watermark_mask);

            $details = [
                'watermark_mask' => $watermark_mask,
                'watermark_size' => $request->watermark_size,
                'watermark_opacity' => $request->watermark_opacity,
                'watermark_position' => $request->watermark_position
            ];
            AccountManager::where('user_id', Auth::id())
                ->where('social_network_id', $request->social_network_id)
                ->update(['watermark_details' => $details]);

        } else {
            return $this->fail("Image Type not supported");
        }
    }

    public function getWatermarks()
    {

        $user_watermarks = DB::table('account_manager')
            ->join('social_networks', 'account_manager.social_network_id', '=', 'social_networks.id')
            ->select(
                'account_manager.social_network_id',
                'social_networks.name',
                'social_networks.icon',
                'account_manager.watermark_details',
            )
            ->where('account_manager.user_id', Auth::id())
            ->get();


        $custom_arr = [];
        foreach ($user_watermarks as $user_watermark) {

            $customobj = json_decode($user_watermark->watermark_details);

            if (isset($user_watermark->watermark_details)) {
                $modifydata = [
                    'watermark_mask' => ImageToken::getToken($customobj->watermark_mask),
                    'watermark_size' => $customobj->watermark_size,
                    'watermark_opacity' => $customobj->watermark_opacity,
                    'watermark_position' => $customobj->watermark_position
                ];
            } else {
                $modifydata = null;
            }
            $custom_arr[] = [
                'social_network_id' => $user_watermark->social_network_id,
                'name' => $user_watermark->name,
                'icon' => $user_watermark->icon,
                'watermark_details' => $modifydata
            ];
        }

        return $this->success('SUCCESS', ['watermarks' => $custom_arr]);
    }
}
