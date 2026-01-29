<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ReportService
{
    public function saveImage($file)
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = time().'_'.uniqid().'.'.$extension;
        $filePath = 'reports/'.$fileName;

        /* Handle compressed image */
        $manager = new ImageManager(new Driver);
        $image = $manager->read($file);

        $image->scale(width: 1920);

        $quality = 90;
        $targetSize = 1024 * 1024;
        $minQuality = 20;

        $encoded = null;

        while ($quality >= $minQuality) {
            if (in_array($extension, ['png'])) {
                $encoded = $image->toPng();
            } else {
                $encoded = $image->toJpeg($quality);
            }

            $currentSize = strlen($encoded);

            if ($currentSize <= $targetSize) {
                break;
            }

            $quality -= 5;
        }

        /* Save Image */
        Storage::put($filePath, (string) $encoded);

        return $filePath;
    }
}
