<?php

namespace App\Traits;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

trait CompressesImages
{
    public function compressAndStoreImage($image, $path, $width = null, $height = null, $maxFileSize = 1048576) // 1MB in bytes
    {
        $imageInstance = Image::make($image);

        // Cek ukuran file dan resize jika melebihi batas
        while (strlen((string) $imageInstance->encode()) > $maxFileSize) {
            // Resize dengan mengurangi dimensi hingga ukuran di bawah 1MB
            $imageInstance->resize(
                $imageInstance->width() * 0.9, // Kurangi lebar 10%
                $imageInstance->height() * 0.9, // Kurangi tinggi 10%
                function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            );
        }

        // Encode ulang dengan kompresi jika diperlukan
        $compressedImage = $imageInstance->encode('jpg', 75); // 75% kualitas

        // Simpan gambar ke penyimpanan
        Storage::put($path, (string) $compressedImage);

        return $path;
    }
}
