<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageToken
{
    public static function getToken($token){
        $client = Storage::disk('s3')
            ->getDriver()
            ->getAdapter()
            ->getClient();

        $expiry = "+5 hours";

        $command = $client->getCommand('GetObject', [
            'Bucket' => env('AWS_BUCKET'),
            'Key'    => $token
        ]);

        $request = $client->createPresignedRequest($command, $expiry);

        return (string) $request->getUri();
    }
}
