<?php

use App\Model\FileManager;
use Illuminate\Database\Seeder;

class FileManagerDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $files = [
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'name' => 'Introducing Chromecast',
                'url' => 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',
                'lazy' => null,
                'extension' => 'mp4',
                'size' => 38743840.00,
                'type' => 'video',
                'width' => 960,
                'height' => 540,
                'sub' => 1,
                'resource_type' => 1,
            ],            [
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'name' => 'Sintel',
                'url' => 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4',
                'lazy' => null,
                'extension' => 'mp4',
                'size' => 38743840.00,
                'type' => 'video',
                'width' => 960,
                'height' => 540,
                'sub' => 1,
                'resource_type' => 1,
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'name' => 'wolfgang-hasselmann-WrVvYxq11Yk-unsplash',
                'url' => 'https://picsum.photos/500/300?image=1',
                'lazy' => 'https://picsum.photos/50/30?image=1',
                'extension' => 'jpg',
                'size' => 980067.00,
                'type' => 'image',
                'width' => 500,
                'height' => 300,
                'sub' => 1,
                'resource_type' => 1,
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'name' => 'willian-justen-de-vasconcellos-ASKGjAeIY_U-unsplash',
                'url' => 'https://picsum.photos/500/300?image=2',
                'lazy' => 'https://picsum.photos/50/30?image=2',
                'extension' => 'jpg',
                'size' => 701641.00,
                'type' => 'image',
                'width' => 500,
                'height' => 300,
                'sub' => 1,
                'resource_type' => 1,
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'name' => 'Sample 1',
                'url' => 'https://picsum.photos/500/300?image=3',
                'lazy' => 'https://picsum.photos/50/30?image=3',
                'extension' => 'jpg',
                'size' => 3915.00,
                'type' => 'image',
                'width' => 500,
                'height' => 300,
                'sub' => 1,
                'resource_type' => 1,
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'name' => 'Sample 2',
                'url' => 'https://picsum.photos/500/300?image=4',
                'lazy' => 'https://picsum.photos/50/30?image=4',
                'extension' => 'jpg',
                'size' => 980067.00,
                'type' => 'image',
                'width' => 500,
                'height' => 300,
                'sub' => 1,
                'resource_type' => 1,
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => 'aa7ef36f-2632-36fb-a4f8-b7901ef20f5a',
                'name' => 'Sample 2',
                'url' => 'https://picsum.photos/500/300?image=5',
                'lazy' => 'https://picsum.photos/50/30?image=5',
                'extension' => 'jpg',
                'size' => 980067.00,
                'type' => 'image',
                'width' => 500,
                'height' => 300,
                'sub' => 1,
                'resource_type' => 1,
            ],
        ];
        FileManager::insert($files);
        foreach ($files as $file){
            $file['id'] = \Illuminate\Support\Str::uuid();
            $file['user_id'] = 'dd14aa6f-2632-4acb-a4f8-c790eef30f50';
            FileManager::create($file);
        }
    }
}
