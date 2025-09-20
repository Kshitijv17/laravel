<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImageStorageService;
use App\Traits\HandlesImageUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    use HandlesImageUploads;

    /**
     * Upload an image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'folder' => 'sometimes|string|max:100',
        ]);

        try {
            $folder = $request->input('folder', 'uploads');
            $file = $request->file('image');
            
            // Generate a unique folder name if not provided
            if (empty($folder)) {
                $folder = 'uploads/' . now()->format('Y/m/d');
            }

            // Upload the image
            $result = $this->uploadImage($file, $folder, [
                'width' => $request->input('width'),
                'height' => $request->input('height'),
                'quality' => $request->input('quality', 85),
                'constraint' => $request->boolean('constrain_aspect_ratio', true),
            ]);

            if (!$result) {
                throw new \Exception('Failed to process the uploaded image.');
            }

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => [
                    'path' => $result['path'],
                    'url' => $result['url'],
                    'filename' => $result['filename'],
                    'original_name' => $result['original_name'],
                    'mime_type' => $result['mime_type'],
                    'size' => $result['size'],
                    'disk' => $result['disk'],
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Image upload error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload a base64 encoded image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadBase64(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            'folder' => 'sometimes|string|max:100',
        ]);

        try {
            $folder = $request->input('folder', 'uploads');
            $base64Image = $request->input('image');
            
            // Generate a unique folder name if not provided
            if (empty($folder)) {
                $folder = 'uploads/' . now()->format('Y/m/d');
            }

            // Upload the base64 image
            $result = $this->uploadBase64Image($base64Image, $folder, [
                'width' => $request->input('width'),
                'height' => $request->input('height'),
                'quality' => $request->input('quality', 85),
                'constraint' => $request->boolean('constrain_aspect_ratio', true),
            ]);

            if (!$result) {
                throw new \Exception('Failed to process the base64 image.');
            }

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => [
                    'path' => $result['path'],
                    'url' => $result['url'],
                    'filename' => $result['filename'],
                    'original_name' => $result['original_name'],
                    'mime_type' => $result['mime_type'],
                    'size' => $result['size'],
                    'disk' => $result['disk'],
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Base64 image upload error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an uploaded image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        try {
            $deleted = $this->deleteImage($request->input('path'));

            if (!$deleted) {
                throw new \Exception('Failed to delete the image.');
            }

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Image deletion error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage()
            ], 500);
        }
    }
}
