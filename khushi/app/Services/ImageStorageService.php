<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;

class ImageStorageService
{
    protected $disk;
    protected $rootFolder;
    protected $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];
    protected $maxFileSize = 5120; // 5MB in KB

    public function __construct(string $disk = null, string $rootFolder = 'uploads')
    {
        $this->disk = $disk ?? config('filesystems.default');
        $this->rootFolder = rtrim($rootFolder, '/');
    }

    /**
     * Upload an image to the specified path
     *
     * @param UploadedFile $file
     * @param string $subfolder
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public function upload(UploadedFile $file, string $subfolder = '', array $options = []): array
    {
        $this->validateImage($file);

        $filename = $this->generateUniqueFilename($file);
        $folderPath = $this->buildPath($subfolder);
        $fullPath = "{$folderPath}/{$filename}";

        // Process image if needed (resize, optimize, etc.)
        $processedImage = $this->processImage($file, $options);

        // Store the file
        $stored = Storage::disk($this->disk)->put(
            $fullPath,
            $processedImage->stream()->getContents()
        );

        if (!$stored) {
            throw new \Exception('Failed to upload image');
        }

        return [
            'path' => $fullPath,
            'url' => $this->getUrl($fullPath),
            'filename' => $filename,
            'original_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'disk' => $this->disk,
        ];
    }

    /**
     * Delete an image
     */
    public function delete(string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        return Storage::disk($this->disk)->delete($path);
    }

    /**
     * Get the public URL of a stored image
     */
    public function getUrl(string $path): string
    {
        if (empty($path)) {
            return '';
        }

        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Generate a unique filename
     */
    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        
        return $filename . '-' . time() . '-' . Str::random(10) . '.' . $extension;
    }

    /**
     * Validate the uploaded file
     *
     * @throws \Exception
     */
    protected function validateImage(UploadedFile $file): void
    {
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            throw new \Exception('Invalid file type. Allowed types: ' . implode(', ', $this->allowedMimeTypes));
        }

        if ($file->getSize() > ($this->maxFileSize * 1024)) {
            throw new \Exception("File size exceeds maximum allowed size of {$this->maxFileSize}KB");
        }
    }

    /**
     * Process the image (resize, optimize, etc.)
     */
    protected function processImage(UploadedFile $file, array $options = [])
    {
        $image = Image::make($file);

        // Apply options if provided
        if (isset($options['width']) || isset($options['height'])) {
            $width = $options['width'] ?? null;
            $height = $options['height'] ?? null;
            $constraint = $options['constraint'] ?? true;
            
            $image->resize($width, $height, function ($constraint) use ($options) {
                if (($options['constraint'] ?? true)) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            });
        }

        // Apply quality if specified
        $quality = $options['quality'] ?? 90;
        $image->encode(null, $quality);

        return $image;
    }

    /**
     * Build the full storage path
     */
    protected function buildPath(string $subfolder = ''): string
    {
        $path = $this->rootFolder;
        
        if (!empty($subfolder)) {
            $path .= '/' . trim($subfolder, '/');
        }
        
        return $path;
    }
}
