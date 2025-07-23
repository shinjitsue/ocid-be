<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\College;
use App\Models\Undergrad;
use App\Models\Graduate;
use App\Models\Curriculum;
use App\Models\Syllabus;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all dashboard data with optimized caching strategy
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'dashboard_data_v6';
        $shortCacheKey = 'dashboard_quick_v6';
        
        // Check if client wants fresh data
        $forceRefresh = $request->boolean('force_refresh', false);
        
        if (!$forceRefresh) {
            // Try to get cached data first
            $cachedData = $this->getCachedData($cacheKey);
            if ($cachedData) {
                return $this->successResponse($cachedData, 'Dashboard data retrieved from cache');
            }
        }

        try {
            // Use database transactions for consistency
            $data = DB::transaction(function () {
                return $this->fetchFreshData();
            });

            // Cache the data with multiple strategies
            $this->cacheData($cacheKey, $data);
            
            return $this->successResponse($data, 'Dashboard data retrieved successfully');
            
        } catch (\Exception $e) {
            \Log::error('Dashboard data fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Try to return stale cache as fallback
            $staleData = $this->getStaleCache($cacheKey);
            if ($staleData) {
                return $this->successResponse($staleData, 'Dashboard data retrieved from fallback cache');
            }
            
            throw $e;
        }
    }

    private function fetchFreshData(): array
    {
        // Optimized single query approach with proper indexing
        $colleges = DB::table('colleges as c')
            ->leftJoin('campuses as camp', 'c.campus_id', '=', 'camp.id')
            ->leftJoin('undergrads as u', 'c.id', '=', 'u.college_id')
            ->leftJoin('graduates as g', 'c.id', '=', 'g.college_id')
            ->select([
                'c.id', 'c.name', 'c.acronym', 'c.campus_id', 
                'c.logo_url', 'c.created_at', 'c.updated_at',
                'camp.name as campus_name', 
                'camp.acronym as campus_acronym',
                DB::raw('COUNT(DISTINCT u.id) as undergraduate_programs_count'),
                DB::raw('COUNT(DISTINCT g.id) as graduate_programs_count')
            ])
            ->groupBy([
                'c.id', 'c.name', 'c.acronym', 'c.campus_id', 
                'c.logo_url', 'c.created_at', 'c.updated_at',
                'camp.name', 'camp.acronym'
            ])
            ->get()
            ->map(function ($college) {
                return [
                    'id' => $college->id,
                    'name' => $college->name,
                    'acronym' => $college->acronym,
                    'campus_id' => $college->campus_id,
                    'logo_url' => $college->logo_url,
                    'created_at' => $college->created_at,
                    'updated_at' => $college->updated_at,
                    'campus' => [
                        'name' => $college->campus_name,
                        'acronym' => $college->campus_acronym
                    ],
                    'undergraduate_programs_count' => (int)$college->undergraduate_programs_count,
                    'graduate_programs_count' => (int)$college->graduate_programs_count,
                    'programs' => (int)$college->undergraduate_programs_count + (int)$college->graduate_programs_count,
                    'files' => 0 // Will be calculated if needed
                ];
            });

        return [
            'campuses' => Campus::select(['id', 'name', 'acronym', 'address'])->get(),
            'colleges' => $colleges,
            'undergrads' => Undergrad::with(['college:id,name,acronym,campus_id'])->get(),
            'graduates' => Graduate::with(['college:id,name,acronym,campus_id'])->get(),
            'curriculum' => Curriculum::with([
                'undergradProgram:id,program_name,college_id',
                'graduateProgram:id,program_name,college_id',
                'undergradProgram.college:id,name,acronym',
                'graduateProgram.college:id,name,acronym'
            ])->get(),
            'syllabus' => Syllabus::with([
                'undergradProgram:id,program_name,college_id',
                'graduateProgram:id,program_name,college_id',
                'undergradProgram.college:id,name,acronym',
                'graduateProgram.college:id,name,acronym'
            ])->get(),
            'meta' => [
                'cached_at' => now()->toISOString(),
                'version' => 'v6',
                'total_records' => $colleges->count()
            ]
        ];
    }

    private function getCachedData(string $key): ?array
    {
        try {
            if (config('cache.default') === 'redis') {
                return Cache::get($key);
            } else {
                // For non-Redis cache drivers, use simple caching without tags
                return Cache::get($key);
            }
        } catch (\Exception $e) {
            \Log::warning('Cache retrieval failed', ['key' => $key, 'error' => $e->getMessage()]);
            return null;
        }
    }

    private function cacheData(string $key, array $data): void
    {
        try {
            $ttl = config('app.env') === 'production' ? 900 : 300; // 15min prod, 5min dev
            
            if (config('cache.default') === 'redis') {
                // Use tags with Redis for better cache invalidation
                Cache::tags(['dashboard', 'colleges', 'programs'])->put($key, $data, $ttl);
            } else {
                // Simple cache without tags for other drivers
                Cache::put($key, $data, $ttl);
            }
            
            // Store a quick access version with essential data only
            $quickData = [
                'colleges_count' => count($data['colleges']),
                'programs_count' => count($data['undergrads']) + count($data['graduates']),
                'files_count' => count($data['curriculum']) + count($data['syllabus']),
                'last_updated' => now()->toISOString()
            ];
            Cache::put($key . '_quick', $quickData, $ttl * 2);
            
        } catch (\Exception $e) {
            \Log::warning('Cache storage failed', ['key' => $key, 'error' => $e->getMessage()]);
        }
    }

    private function getStaleCache(string $key): ?array
    {
        try {
            // Try to get stale cache (double the TTL)
            $staleKey = $key . '_stale';
            return Cache::get($staleKey);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get summary statistics only (faster endpoint)
     */
    public function summary(): JsonResponse
    {
        $cacheKey = 'dashboard_summary_v2';
        
        $data = Cache::remember($cacheKey, 600, function () {
            return [
                'colleges_count' => College::count(),
                'undergrad_programs_count' => Undergrad::count(),
                'graduate_programs_count' => Graduate::count(),
                'curriculum_files_count' => Curriculum::whereNotNull('file_path')->count(),
                'syllabus_files_count' => Syllabus::whereNotNull('file_path')->count(),
                'total_files_count' => Curriculum::whereNotNull('file_path')->count() + 
                                     Syllabus::whereNotNull('file_path')->count(),
                'last_updated' => now()->toISOString()
            ];
        });

        return $this->successResponse($data, 'Dashboard summary retrieved successfully');
    }

    /**
     * Invalidate dashboard cache intelligently
     */
    public function clearCache(Request $request): JsonResponse
    {
        try {
            $cachePattern = $request->input('pattern', 'dashboard_*');
            
            if (config('cache.default') === 'redis') {
                // Clear tagged cache
                Cache::tags(['dashboard', 'colleges', 'programs'])->flush();
            } else {
                // Clear specific keys for non-Redis drivers
                $keys = [
                    'dashboard_data_v6',
                    'dashboard_data_v6_quick',
                    'dashboard_data_v6_stale',
                    'dashboard_summary_v2'
                ];
                
                foreach ($keys as $key) {
                    Cache::forget($key);
                }
            }
            
            return $this->successResponse(null, 'Dashboard cache cleared successfully');
            
        } catch (\Exception $e) {
            \Log::error('Cache clear failed', ['error' => $e->getMessage()]);
            return $this->errorResponse('Failed to clear cache', 500);
        }
    }

    /**
     * Health check endpoint for monitoring
     */
    public function health(): JsonResponse
    {
        try {
            $health = [
                'status' => 'ok',
                'cache_driver' => config('cache.default'),
                'cache_working' => $this->testCache(),
                'database_working' => $this->testDatabase(),
                'timestamp' => now()->toISOString()
            ];
            
            return response()->json($health);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function testCache(): bool
    {
        try {
            $testKey = 'health_check_' . uniqid();
            Cache::put($testKey, 'test', 10);
            $result = Cache::get($testKey) === 'test';
            Cache::forget($testKey);
            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function testDatabase(): bool
    {
        try {
            DB::select('SELECT 1');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}