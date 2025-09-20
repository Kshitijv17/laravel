<?php

namespace App\Console\Commands;

use App\Services\ImageStorageService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;

class TestImageUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:test-upload {path : Path to the test image file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the image upload functionality';

    /**
     * The image storage service instance.
     *
     * @var ImageStorageService
     */
    protected $imageStorage;

    /**
     * Create a new command instance.
     *
     * @param ImageStorageService $imageStorage
     * @return void
     */
    public function __construct(ImageStorageService $imageStorage)
    {
        parent::__construct();
        $this->imageStorage = $imageStorage;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->argument('path');
        
        if (!file_exists($path)) {
            $this->error("The specified file does not exist: {$path}");
            return 1;
        }

        $this->info("Testing image upload for: {$path}");
        
        // Create an UploadedFile instance
        $file = new UploadedFile(
            $path,
            basename($path),
            mime_content_type($path),
            null,
            true // Mark as test to prevent moving the file
        );

        try {
            // Upload the image
            $result = $this->imageStorage->upload($file, 'test-uploads', [
                'width' => 800,
                'height' => 600,
                'quality' => 85
            ]);

            $this->info("\n✅ Image uploaded successfully!");
            $this->line("\nUpload Details:");
            $this->table(
                ['Property', 'Value'],
                [
                    ['Path', $result['path']],
                    ['URL', $result['url']],
                    ['Filename', $result['filename']],
                    ['Original Name', $result['original_name']],
                    ['MIME Type', $result['mime_type']],
                    ['Size', $this->formatBytes($result['size'])],
                    ['Storage Disk', $result['disk']],
                ]
            );

            return 0;
        } catch (\Exception $e) {
            $this->error("\n❌ Upload failed: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Format bytes to a human-readable format.
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
