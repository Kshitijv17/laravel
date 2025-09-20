<?php

namespace App\Traits;

use App\Services\ImageStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Trait HandlesImageUploads
 * 
 * Provides reusable methods for handling image uploads in controllers.
 */
trait HandlesImageUploads
{
    /**
     * The image storage service instance.
     *
     * @var ImageStorageService
     */
    protected $imageStorage;

    /**
     * Initialize the image storage service.
     *
     * @param ImageStorageService $imageStorage
     * @return void
     */
    public function __construct(ImageStorageService $imageStorage)
    {
        $this->imageStorage = $imageStorage;
    }

    /**
     * Upload an image and return the file information.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param array $options
     * @return array|null
     */
    protected function uploadImage(UploadedFile $file, string $folder = '', array $options = []): ?array
    {
        try {
            return $this->imageStorage->upload($file, $folder, $options);
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete an image by its path.
     *
     * @param string|null $path
     * @return bool
     */
    protected function deleteImage(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        try {
            return $this->imageStorage->delete($path);
        } catch (\Exception $e) {
            Log::error('Image deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle multiple image uploads.
     *
     * @param array $files
     * @param string $folder
     * @param array $options
     * @return array
     */
    protected function uploadImages(array $files, string $folder = '', array $options = []): array
    {
        $uploaded = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $result = $this->uploadImage($file, $folder, $options);
                if ($result) {
                    $uploaded[] = $result;
                }
            }
        }
        
        return $uploaded;
    }

    /**
     * Get the public URL for an image.
     *
     * @param string|null $path
     * @return string
     */
    protected function getImageUrl(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        try {
            return $this->imageStorage->getUrl($path);
        } catch (\Exception $e) {
            Log::error('Failed to get image URL: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Handle image upload from base64 string.
     *
     * @param string $base64Image
     * @param string $folder
     * @param array $options
     * @return array|null
     */
    protected function uploadBase64Image(string $base64Image, string $folder = '', array $options = []): ?array
    {
        try {
            // Extract the base64 content and extension
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
                $extension = $matches[1];
                $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
                $imageData = base64_decode($imageData);
                
                // Create a temporary file
                $tempFile = tempnam(sys_get_temp_dir(), 'img_');
                file_put_contents($tempFile, $imageData);
                
                // Create UploadedFile instance
                $file = new UploadedFile(
                    $tempFile,
                    'image.' . $extension,
                    'image/' . $extension,
                    null,
                    true // Mark as test to prevent moving the file
                );
                
                // Upload the file
                return $this->uploadImage($file, $folder, $options);
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Base64 image upload failed: ' . $e->getMessage());
            return null;
        }
    }
}
