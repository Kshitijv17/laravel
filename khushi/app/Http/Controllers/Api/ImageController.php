<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImageStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    protected $imageService;

    public function __construct(ImageStorageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Upload an image
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB max
            'folder' => 'sometimes|string|max:100',
        ]);

        try {
            $file = $request->file('image');
            $folder = $request->input('folder', 'general');
            
            $image = $this->imageService->upload($file, $folder, [
                'width' => 1200,
                'height' => 800,
                'constraint' => true,
                'quality' => 85
            ]);

            return response()->json([
                'success' => true,
                'data' => $image,
                'message' => 'Image uploaded successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete an image
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
            $deleted = $this->imageService->delete($request->path);

            if (!$deleted) {
                throw new \Exception('Failed to delete image');
            }

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
