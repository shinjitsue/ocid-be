<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\College;
use App\Models\Undergrad;
use App\Models\Graduate;
use App\Models\Curriculum;
use App\Models\Syllabus;
use App\Models\Form;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        try {
            Log::info('Dashboard API called');

            $cacheKey = 'dashboard_data_v6';
            $forceRefresh = $request->boolean('force_refresh', false);

            if (!$forceRefresh) {
                $cachedData = $this->getCachedData($cacheKey);
                if ($cachedData) {
                    Log::info('Returning cached dashboard data');
                    return $this->successResponse($cachedData, 'Dashboard data retrieved from cache');
                }
            }

            // Fetch data with proper error handling
            $data = $this->fetchFreshData();
            
            // Cache the data
            $this->cacheData($cacheKey, $data);
            
            Log::info('Dashboard data fetched successfully', [
                'campuses_count' => count($data['campuses']),
                'colleges_count' => count($data['colleges']),
                'forms_count' => count($data['forms']),
            ]);

            return $this->successResponse($data, 'Dashboard data retrieved successfully');
            
        } catch (\Exception $e) {
            Log::error('Dashboard data fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse('Failed to fetch dashboard data: ' . $e->getMessage(), 500);
        }
    }

    private function fetchFreshData(): array
    {
        try {
            // Fetch basic data first
            $campuses = Campus::select(['id', 'name', 'acronym', 'address'])->get();
            Log::info('Campuses fetched', ['count' => $campuses->count()]);

            // Fetch colleges with campus relationship
            $colleges = College::with(['campus:id,name,acronym'])
                ->select(['id', 'name', 'acronym', 'campus_id', 'logo_path', 'created_at'])
                ->get();
            Log::info('Colleges fetched', ['count' => $colleges->count()]);

            // Fetch undergraduate programs
            $undergrads = Undergrad::with(['college:id,name,acronym,campus_id'])
                ->select(['id', 'program_name', 'acronym', 'college_id', 'created_at'])
                ->get();
            Log::info('Undergrad programs fetched', ['count' => $undergrads->count()]);

            // Fetch graduate programs
            $graduates = Graduate::with(['college:id,name,acronym,campus_id'])
                ->select(['id', 'program_name', 'acronym', 'college_id', 'created_at'])
                ->get();
            Log::info('Graduate programs fetched', ['count' => $graduates->count()]);

            // Fetch curriculum files
            $curriculum = Curriculum::select([
                'id', 'program_id', 'program_type', 'file_path', 'file_url', 
                'file_name', 'file_type', 'file_size', 'created_at'
            ])->get();
            Log::info('Curriculum files fetched', ['count' => $curriculum->count()]);

            // Fetch syllabus files
            $syllabus = Syllabus::select([
                'id', 'program_id', 'program_type', 'file_path', 'file_url', 
                'file_name', 'file_type', 'file_size', 'created_at'
            ])->get();
            Log::info('Syllabus files fetched', ['count' => $syllabus->count()]);

            // Fetch forms
            $forms = Form::select([
                'id', 'form_number', 'title', 'purpose', 'link', 'revision',
                'file_path', 'file_url', 'file_name', 'file_type', 'file_size', 'created_at'
            ])->orderBy('form_number')->get();
            Log::info('Forms fetched', ['count' => $forms->count()]);

            return [
                'campuses' => $campuses,
                'colleges' => $colleges,
                'undergrads' => $undergrads,
                'graduates' => $graduates,
                'curriculum' => $curriculum,
                'syllabus' => $syllabus,
                'forms' => $forms,
                'meta' => [
                    'timestamp' => now()->toISOString(),
                    'total_records' => $campuses->count() + $colleges->count() + 
                                    $undergrads->count() + $graduates->count() + 
                                    $curriculum->count() + $syllabus->count() + $forms->count()
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error in fetchFreshData', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            throw $e;
        }
    }

    private function getCachedData(string $key): ?array
    {
        try {
            return Cache::get($key);
        } catch (\Exception $e) {
            Log::warning('Cache retrieval failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function cacheData(string $key, array $data): void
    {
        try {
            $ttl = config('app.env') === 'production' ? 900 : 300;
            Cache::put($key, $data, $ttl);
        } catch (\Exception $e) {
            Log::warning('Cache storage failed', ['error' => $e->getMessage()]);
        }
    }

    public function clearCache(Request $request): JsonResponse
    {
        try {
            Cache::forget('dashboard_data_v6');
            return $this->successResponse(null, 'Dashboard cache cleared successfully');
        } catch (\Exception $e) {
            Log::error('Cache clear failed', ['error' => $e->getMessage()]);
            return $this->errorResponse('Failed to clear cache', 500);
        }
    }
}