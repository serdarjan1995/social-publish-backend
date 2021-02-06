<?php

namespace App\Http\Controllers\API\FileManager;

use App\Helpers\RoleHelper;
use App\Model\FileManager;
use App\Http\Controllers\ApiController;
use App\User;
use FFMpeg\FFProbe;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\ImageManagerStatic as Image;

class UploadFile extends ApiController
{
    protected $resource_type;

    public function imageToken($url)
    {
        $s3 = Storage::disk('s3');
        $client = $s3->getDriver()->getAdapter()->getClient();
        $expiry = "+5 hours";

        $command = $client->getCommand('GetObject', [
            'Bucket' => env('AWS_BUCKET'),
            'Key' => $url
        ]);

        $request = $client->createPresignedRequest($command, $expiry);
        return (string)$request->getUri();
    }

    public function fileToBase64($url)
    {
        $path = $url;
        $type = "png";//pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public function resourceType($resource_type)
    {
        /*
         * 1: File Manager
         * 2: Ticket
         */
        if (!isset($resource_type)) {
            $this->resource_type = 1;
        } else {
            $this->resource_type = $resource_type;
        }
        return;
    }

    public function userFiles(Request $request)
    {

        RoleHelper::need('file_managers_show');
        $userFiles = FileManager::select('id', 'name', 'url', 'lazy', 'type', 'sub', 'created_at')
            ->where("resource_type", 1)
            ->where('user_id', Auth::id())
            ->get();
        foreach ($userFiles as $key => $data) {
            //$data['url']     =   $this->imageToken($data->url);
            $data['url'] = $data->url;
            //$data['lazy'] = $data->type == 'image' ? $this->imageToken($data->lazy) : null;
            $data['lazy'] = $data->lazy;
        }
        return $this->success(null, ['files' => $userFiles]);
    }

    public function userEndFile(Request $request)
    {

        RoleHelper::need('file_managers_show');
        $userFiles = FileManager::orderBy('created_at', 'desc')
            ->select('id', 'name', 'url', 'lazy', 'type', 'sub', 'created_at')
            ->where("resource_type", 1)
            ->where('user_id', Auth::id())
            ->first();

        $userFiles['url'] = //$this->imageToken($userFiles->url);
        $userFiles['url'] = $userFiles->url;
        $userFiles['lazy'] = $userFiles->type == 'image' ? $this->imageToken($userFiles->lazy) : null;
        $userFiles['base64'] = $this->fileToBase64($userFiles->url);

        return $this->success(null, ['files' => $userFiles]);
    }

    public function getFileBase(Request $request)
    {
        return $this->fileToBase64($request->url);
    }

    public function updateImageUser(Request $request)
    {

        if (!empty($request->file_avatar)) {
            $file_avatar = $request->file_avatar->store('avatars', 's3');
            Storage::disk('s3')->setVisibility($file_avatar, 'private');

            User::where('id', Auth::id())->update([
                'profile_image' => $file_avatar
            ]);

            $file_avatar = $this->imageToken($file_avatar);
            return $this->success('Success updated', ['avatar' => $file_avatar]);
        } else {
            return $this->fail('Not found');
        }

    }

    public function amazonUploadFile(Request $requestPath)
    {
        $bucket_path = env('AWS_BUCKET_DIR') . '/' . Auth::id();
        return Storage::disk('s3')->put($bucket_path, $requestPath->file('file'), 'private');
    }

    public function create(Request $request)
    {
        $this->resourceType($request->resource_type);

        $file = $request->file('file')->getClientOriginalName();
        $filetype = pathinfo($file, PATHINFO_EXTENSION);
        $filename = pathinfo($file, PATHINFO_FILENAME);
        $size = $request->file('file')->getSize();

        if ($this->fileType($filetype)) {

            $bucket_path = env('AWS_BUCKET_DIR') . '/' . Auth::id();
            $path = $this->amazonUploadFile($request);

            $availableVideoType = $this->availableVideoType();
            if (in_array($filetype, $availableVideoType)) {
                // VIDEO

                $ffprobe = FFProbe::create();
                $filedetails = $request->file('file');
                $videoInfo = $ffprobe
                    ->streams($filedetails)
                    ->videos()
                    ->first();

                $width = $videoInfo->get('width');
                $height = $videoInfo->get('height');
                $type = "video";

                $dbData = [
                    'name' => $filename,
                    'url' => $path,
                    'lazy' => null,
                    'extension' => $filetype,
                    'size' => $size,
                    'type' => $type,
                    'width' => $width,
                    'height' => $height
                ];
                $create = $this->dbCreate($dbData);
                if ($create) {
                    if ($this->resource_type == 2) {
                        return $create['id'];
                    }
                    return $this->success("Video upload success", ['files' => $create]);
                } else {
                    return $this->fail("Video upload error");
                }

            } else {
                // IMAGE

                $file = request()->file('file');
                $imageName = uniqid(date('YmdHis')) . '.' . $file->getClientOriginalName();
                $img = Image::make($file);
                $img->resize(null, 100, function ($constraint) {
                    $constraint->aspectRatio();
                });

                //detach method is the key! Hours to find it... :/
                $resource = $img->stream()->detach();
                $lazyUrl = Storage::disk('s3')->put(
                    $bucket_path . "/lazy/" . $imageName, $resource, 'private'
                );

                $lazyUrl = $bucket_path . "/lazy/" . $imageName;

                $imagewidth = getimagesize($request->file('file'))[0];
                $imageheight = getimagesize($request->file('file'))[1];
                $type = "image";

                $dbData = [
                    'name' => $filename,
                    'url' => $path,
                    'lazy' => $lazyUrl,
                    'extension' => $filetype,
                    'size' => $size,
                    'type' => $type,
                    'width' => $imagewidth,
                    'height' => $imageheight
                ];
                $create = $this->dbCreate($dbData);
                if ($create) {
                    if ($this->resource_type == 2) {
                        return $create['id'];
                    }
                    $create['url'] = $this->imageToken($create['url']);
                    $create['lazy'] = $this->imageToken($create['lazy']);
                    //$create['base64'] = $this->fileToBase64($create['url']);

                    return $this->success("Image upload success", ["files" => $create]);
                } else {
                    return $this->fail("Image upload error");
                }
            }
        } else {
            return $this->fail("Type not available");
        }
    }

    public function userFileHistory(Request $request)
    {
        $original_file = FileManager::where([
            ['user_id', Auth::id()],
            ['id', $request->id],])
            ->first();

        if (!$original_file) {
            return $this->fail(trans('global.not_found'));
        }
        $search_id = $original_file->id;
        $user_history = FileManager::select('id', 'name', 'url', 'lazy', 'type', 'sub', 'created_at')
            ->where('user_id', Auth::id())
            ->where(function ($query) use ($search_id) {
                $query->orWhere('id', $search_id)
                    ->orWhere('sub', $search_id);
            })
            ->orderBy('created_at')
            ->get();

        foreach ($user_history as $key => $data) {
            $data['url'] = $this->imageToken($data->url);
            $data['lazy'] = $this->imageToken($data->lazy);
        }

        return $this->success(null, [
            'history' => $user_history,
            'get_id' => $original_file->id,
            'sub' => $original_file->sub
        ]);
    }

    public function deleteUserFile(Request $request)
    {

        $delete_item = FileManager::where("id", $request->id)->where('user_id', Auth::id())->first();
        if ($delete_item) {
            $delete_action = Storage::disk('s3')->delete($delete_item->url);
            if ($delete_action) {
                $db_image_delete = $delete_item->delete();
                if ($db_image_delete) {
                    return $this->success('OK');
                } else {
                    return $this->success('Database delete failed');
                }
            } else {
                return $this->fail('Delete AWZ failed');
            }
        } else {
            return $this->fail('User Image Not Found');
        }
    }

    public function availableFileType()
    {
        return array_merge($this->availableVideoType(), $this->availableImageType());
    }

    public function availableVideoType()
    {
        return array("mp4");
    }

    public function availableImageType()
    {
        return array("jpg", "jpeg", "png");
    }

    public function dbCreate($data)
    {

        $create = FileManager::create([
            'user_id' => Auth::id(),
            'name' => $data['name'],
            'url' => $data['url'],
            'lazy' => $data['lazy'],
            'extension' => $data['extension'],
            'size' => $data['size'],
            'type' => $data['type'],
            'width' => $data['width'],
            'height' => $data['height'],
            'sub' => isset($data['sub']) ? $data['sub'] : null,
            'resource_type' => $this->resource_type
        ]);
        return $create;
    }

    private function fileType($type)
    {
        $availableType = $this->availableFileType();
        if (in_array($type, $availableType)) {
            return true;
        } else {
            return false;
        }
    }

}
