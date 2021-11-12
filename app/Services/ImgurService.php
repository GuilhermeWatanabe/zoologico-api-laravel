<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ImgurService
{
    public static function uploadImage($image)
    {
        return Http::withHeaders([
            'Authorization' => 'Client-ID 599b2d427ea9e85'
        ])->post('https://api.imgur.com/3/image', [
            'image' => base64_encode(file_get_contents($image))
        ]);
    }
}
