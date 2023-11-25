<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait Upload
{
    public function UploadFile(UploadedFile $file, $folder = null, $filename = null, $disk = 'public')
    {
        $filename = $filename ?? Str::random(15) . '.' . $file->getClientOriginalExtension();

        return Storage::disk($disk)->putFileAs(
            $folder,
            $file,
            $filename,
        );
    }

    public function deleteFile($path, $disk = 'public')
    {
        Storage::disk($disk)->delete($path);
    }
}