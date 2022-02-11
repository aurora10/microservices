<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ImageUploadRequest;

class ImageController
{
    public function upload(ImageUploadRequest $request) {
        $file = $request->file('image');

        $name = Str::random(10);
        $url = asset(\Storage::putFileAs('images', $file, $name . '.' . $file->getClientOriginalExtension())) ;

        return ['url' =>env('APP_URL').  $url];
    }
}
