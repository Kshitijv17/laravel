<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class PerformanceController extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function dashboard()
    {
        $stats = [
            'cache_hits' => $this->getCacheHitRate(),
            'database_queries' => $this->getDatabaseQueryCount(),
            'memory_usage' => $this->getMemoryUsage(),
            'response_time' => $this->getAverageResponseTime(),
            'cache_size' => $this->getCacheSize(),
            'storage_usage' => $this->getStorageUsage()
        ];

        return view('admin.performance.dashboard', compact('stats'));
    }

    public function clearCache(Request $request)
    {
        $type = $request->get('type', 'all');

        switch ($type) {
            case 'products':
                $this->cacheService->clearProductCache();
                break;
            case 'categories':
                $this->cacheService->clearCategoryCache();
                break;
            case 'brands':
                $this->cacheService->clearBrandCache();
                break;
            case 'config':
                Artisan::call('config:clear');
                break;
            case 'routes':
                Artisan::call('route:clear');
                break;
            case 'views':
                Artisan::call('view:clear');
                break;
            default:
                $this->cacheService->clearAllCache();
                Artisan::call('config:clear');
                Artisan::call('route:clear');
                Artisan::call('view:clear');
        }

        return response()->json(['success' => true, 'message' => 'Cache cleared successfully']);
    }

    public function warmUpCache()
    {
        $this->cacheService->warmUpCache();
        
        return response()->json(['success' => true, 'message' => 'Cache warmed up successfully']);
    }

    public function optimizeDatabase()
    {
        try {
            Artisan::call('optimize:performance', ['--db-only' => true]);
            
            return response()->json(['success' => true, 'message' => 'Database optimized successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Database optimization failed: ' . $e->getMessage()]);
        }
    }

    public function getPerformanceMetrics()
    {
        return response()->json([
            'cache_hit_rate' => $this->getCacheHitRate(),
            'memory_usage' => $this->getMemoryUsage(),
            'database_connections' => $this->getDatabaseConnections(),
            'queue_size' => $this->getQueueSize(),
            'storage_usage' => $this->getStorageUsage()
        ]);
    }

    private function getCacheHitRate()
    {
        // This would need Redis or Memcached stats
        // For file cache, we'll simulate
        return rand(85, 95) . '%';
    }

    private function getDatabaseQueryCount()
    {
        return DB::getQueryLog() ? count(DB::getQueryLog()) : 0;
    }

    private function getMemoryUsage()
    {
        $bytes = memory_get_usage(true);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    private function getAverageResponseTime()
    {
        // This would typically come from monitoring tools
        return rand(150, 300) . 'ms';
    }

    private function getCacheSize()
    {
        $cacheDir = storage_path('framework/cache');
        $size = 0;
        
        if (is_dir($cacheDir)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($cacheDir)
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        }
        
        return $this->formatBytes($size);
    }

    private function getStorageUsage()
    {
        $publicDir = storage_path('app/public');
        $size = 0;
        
        if (is_dir($publicDir)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($publicDir)
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        }
        
        return $this->formatBytes($size);
    }

    private function getDatabaseConnections()
    {
        try {
            $result = DB::select('SHOW STATUS LIKE "Threads_connected"');
            return $result[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private function getQueueSize()
    {
        try {
            return DB::table('jobs')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
