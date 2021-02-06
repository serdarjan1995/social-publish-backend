<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use JBZoo\Image\Image;
use Exception;

class WatermarkImage
{

/*    public function use(int $account_id, string $image_token)
    {
        $imData = DB::table('account_manager')
            ->select('*')
            ->where('id', $account_id)
            ->where('user_id', Auth::id())
            ->first();
        $row = json_decode($imData->watermark_details);
        $image_explode = explode('/', $image_token);

        $imgTarget = public_path($image_explode[1] . '_' . $account_id . '_export.jpeg');

        if (file_exists($imgTarget)) {
            return json_encode([
                'type' => 'watermark_image',
                'img' => $imgTarget,
            ]);

        } else {
            if (file_exists(public_path($image_explode[2]))) {
              if (isset($row->watermark_mask)) {
                    $watermark_explode = explode('/', $row->watermark_mask);
                    if (file_exists(public_path($watermark_explode[3]))) {
                        $imgSource = public_path($image_explode[2]);
                        $imgWatermark = public_path($watermark_explode[3]);


                        try {
                            $functionSource = $this->getFunction($imgSource, 'open');
                            $imgSource1 = $functionSource($imgSource);

                            $functionWatermark = $this->getFunction($imgWatermark, 'open');
                            $imgWatermark1 = $functionWatermark($imgWatermark);
                            $sizesSource = $this->getImgSizes($imgSource1);
                            $sizesWatermark = $this->getImgSizes($imgWatermark1);
                            $width = $row->watermark_size / 100 * $sizesSource['width'];
                            $height = ($width / $sizesWatermark['width']) * $sizesWatermark['height'];
                            $imgWatermark_tmp = public_path($account_id . '_watermark.png');


                            $pos = $this->position($row->watermark_position);


                            if (file_exists($imgWatermark_tmp)) {
                                $img = new Image($imgSource);
                                $img->overlay($imgWatermark_tmp, $pos, $row->watermark_opacity, 0, 0);
                                $img->setQuality(100);
                                $img->saveAs($imgTarget, 100);

                                unlink($imgWatermark_tmp);
                                unlink($imgSource);
                                unlink($imgWatermark);
                                return [
                                    'type' => 'watermark_image',
                                    'img' => $imgTarget,
                                ];
                            } else {

                                $resize = new ResizeImage($imgWatermark);
                                $resize->resizeTo($width, $height, 'exact');
                                $resize->saveImage($imgWatermark_tmp);

                                return [
                                    'type' => 'image_customize',
                                    'step' => 3,
                                    'message' => 'Success'
                                ];
                            }


                        } catch (Exception $e) {
                            return [
                                'status' => 'error',
                                'type' => 'watermark',
                                'message' => $e->getMessage()
                            ];
                        }


                    } else {
                        $this->downloadFile($row->watermark_mask);
                        return [
                            'type' => 'image_downloading',
                            'step' => 2,
                            'message' => 'Success'
                        ];
                    }
                } else {
                    return json_encode([
                        'type' => 'default_image',
                        'img' => public_path($image_explode[2])
                    ]);
             //   }

            } else {
                $this->downloadFile($image_token);
                return [
                    'type' => 'image_downloading',
                    'step' => 1,
                    'message' => 'Success'
                ];
            }
        }


    }


    private function downloadFile($amazon_file_token)
    {
        $file_name = basename($amazon_file_token);
        $ex = explode('?', $file_name);
        file_put_contents(public_path($ex[0]), Storage::disk('s3')->get($amazon_file_token));
        return $ex[0];
    }

    private function position(string $position)
    {
        switch ($position) {
            case 'lt':
                $pos = "top left";
                break;

            case 't':
                $pos = "top";
                break;

            case 'rt':
                $pos = "top right";
                break;

            case 'lc':
                $pos = "left";
                break;

            case 'c':
                $pos = "center";
                break;

            case 'rc':
                $pos = "right";
                break;

            case 'rb':
                $pos = "bottom right";
                break;

            case 'b':
                $pos = "bottom";
                break;

            default:
                $pos = "bottom left";
                break;
        }
        return $pos;
    }

    private function getFunction($name, $action = 'open')
    {
        if (preg_match("/^(.*)\.(jpeg|jpg)$/", $name)) {
            if ($action == "open")
                return "imagecreatefromjpeg";
            else
                return "imagejpeg";
        } elseif (preg_match("/^(.*)\.(png)$/", $name)) {
            if ($action == "open")
                return "imagecreatefrompng";
            else
                return "imagepng";
        } elseif (preg_match("/^(.*)\.(gif)$/", $name)) {
            if ($action == "open")
                return "imagecreatefromgif";
            else
                return "imagegif";
        } else {
            throw new Exception('Image an not be found, try another image.');
        }
    }

    private function getImgSizes($img): array
    {
        return [
            'width' => imagesx($img),
            'height' => imagesy($img)
        ];
    }*/
}
