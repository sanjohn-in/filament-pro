<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImageCompressor
{
    public static function compressAndSave(
        TemporaryUploadedFile $file,
        string $directory = 'uploads',
        int $maxWidth = 1920,
        int $quality = 75
    ): string {
        $sourcePath = $file->getRealPath();
        $mime       = mime_content_type($sourcePath);

        // Create GD resource
        $src = match ($mime) {
            'image/png'  => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            'image/gif'  => imagecreatefromgif($sourcePath),
            default      => imagecreatefromjpeg($sourcePath),
        };

        $origW = imagesx($src);
        $origH = imagesy($src);

        // Resize if too large
        if ($origW > $maxWidth) {
            $ratio  = $maxWidth / $origW;
            $newW   = $maxWidth;
            $newH   = (int) round($origH * $ratio);

            $canvas = imagecreatetruecolor($newW, $newH);

            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);

            imagecopyresampled($canvas, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

            imagedestroy($src);
            $src = $canvas;
        }

        $filename = pathinfo($file->hashName(), PATHINFO_FILENAME) . '.jpg';
        $path     = "{$directory}/{$filename}";

        ob_start();
        imagejpeg($src, null, $quality);
        $data = ob_get_clean();

        imagedestroy($src);

        Storage::disk('public')->put($path, $data);

        return $path;
    }
}