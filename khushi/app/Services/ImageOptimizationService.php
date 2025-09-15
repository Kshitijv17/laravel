<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;

class ImageOptimizationService
{
    const THUMBNAIL_SIZE = 300;
    const MEDIUM_SIZE = 600;
    const LARGE_SIZE = 1200;
    const QUALITY = 85;

    public function optimizeAndStore(UploadedFile $file, string $path = 'products'): array
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Create different sizes
        $sizes = [
            'thumbnail' => self::THUMBNAIL_SIZE,
            'medium' => self::MEDIUM_SIZE,
            'large' => self::LARGE_SIZE,
            'original' => null
        ];

        $paths = [];

        foreach ($sizes as $size => $dimension) {
            $sizedFilename = $size . '_' . $filename;
            $fullPath = $path . '/' . $sizedFilename;

            if ($dimension) {
                // Resize and optimize
                $image = Image::make($file)
                    ->resize($dimension, $dimension, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode($file->getClientOriginalExtension(), self::QUALITY);

                Storage::disk('public')->put($fullPath, $image);
            } else {
                // Store original with optimization
                $image = Image::make($file)
                    ->encode($file->getClientOriginalExtension(), self::QUALITY);
                
                Storage::disk('public')->put($fullPath, $image);
            }

            $paths[$size] = $fullPath;
        }

        return $paths;
    }

    public function generateWebP(string $imagePath): string
    {
        $webpPath = pathinfo($imagePath, PATHINFO_DIRNAME) . '/' . 
                   pathinfo($imagePath, PATHINFO_FILENAME) . '.webp';

        if (Storage::disk('public')->exists($imagePath)) {
            $image = Image::make(Storage::disk('public')->path($imagePath))
                ->encode('webp', self::QUALITY);
            
            Storage::disk('public')->put($webpPath, $image);
        }

        return $webpPath;
    }

    public function deleteImageSizes(string $filename, string $path = 'products')
    {
        $sizes = ['thumbnail', 'medium', 'large', 'original'];
        
        foreach ($sizes as $size) {
            $fullPath = $path . '/' . $size . '_' . $filename;
            Storage::disk('public')->delete($fullPath);
            
            // Also delete WebP version
            $webpPath = pathinfo($fullPath, PATHINFO_DIRNAME) . '/' . 
                       pathinfo($fullPath, PATHINFO_FILENAME) . '.webp';
            Storage::disk('public')->delete($webpPath);
        }
    }

    public function getOptimizedImageUrl(string $imagePath, string $size = 'medium'): string
    {
        $pathInfo = pathinfo($imagePath);
        $optimizedPath = $pathInfo['dirname'] . '/' . $size . '_' . $pathInfo['basename'];
        
        if (Storage::disk('public')->exists($optimizedPath)) {
            return Storage::disk('public')->url($optimizedPath);
        }
        
        return Storage::disk('public')->url($imagePath);
    }

    public function getWebPUrl(string $imagePath): ?string
    {
        $webpPath = pathinfo($imagePath, PATHINFO_DIRNAME) . '/' . 
                   pathinfo($imagePath, PATHINFO_FILENAME) . '.webp';
        
        if (Storage::disk('public')->exists($webpPath)) {
            return Storage::disk('public')->url($webpPath);
        }
        
        return null;
    }

    public function compressExistingImages(string $directory = 'products')
    {
        $files = Storage::disk('public')->files($directory);
        
        foreach ($files as $file) {
            if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
                $image = Image::make(Storage::disk('public')->path($file))
                    ->encode(pathinfo($file, PATHINFO_EXTENSION), self::QUALITY);
                
                Storage::disk('public')->put($file, $image);
                
                // Generate WebP version
                $this->generateWebP($file);
            }
        }
    }
}
