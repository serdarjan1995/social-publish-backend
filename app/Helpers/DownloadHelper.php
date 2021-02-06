<?php
namespace App\Helpers;

use App\Model\FileManager;
use Illuminate\Support\Facades\Storage;

class DownloadHelper
{
    public static function download(array $image_ids, string $user_id)
    {
        $arrived = [];

        foreach ($image_ids as $img) {
            $arrived[] = self::downloadFile($img, $user_id);
        }
        return $arrived;
    }

    private static function downloadFile(string $imageID, string $user_id)
    {
        $amazon_file_token = self::convertImageData($imageID, $user_id)->url;
        $file_name = basename($amazon_file_token);
        $ex = explode('?', $file_name);
        $c_name = substr($ex[0], -6);
        file_put_contents(public_path($imageID . '_' . $c_name), Storage::disk('s3')->get($amazon_file_token));
        return $imageID.'_'.$c_name;
    }

    private static function convertImageData(string $imageID, string $user_id)
    {
        return FileManager::where([["id", $imageID],['user_id', $user_id]])->first();
    }

}
