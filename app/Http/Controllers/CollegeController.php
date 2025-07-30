<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Http\Traits\ApiResponseTrait;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CollegeController extends Controller
{
    use ApiResponseTrait;

    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource with optimized queries
     */
    public function index(): JsonResponse
    {
        $cacheKey = 'colleges_with_counts_v2';

        $colleges = Cache::remember($cacheKey, 300, function () {
            return DB::table('colleges as c')
                ->leftJoin('campuses as camp', 'c.campus_id', '=', 'camp.id')
                ->leftJoin('undergrads as u', 'c.id', '=', 'u.college_id')
                ->leftJoin('graduates as g', 'c.id', '=', 'g.college_id')
                ->select(
                    'c.*',
                    'camp.name as campus_name',
                    'camp.acronym as campus_acronym',
                    DB::raw('COUNT(DISTINCT u.id) as undergraduate_programs_count'),
                    DB::raw('COUNT(DISTINCT g.id) as graduate_programs_count')
                )
                ->groupBy('c.id', 'c.name', 'c.acronym', 'c.campus_id', 'c.logo_url', 'c.created_at', 'c.updated_at', 'camp.name', 'camp.acronym')
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
                            'id' => $college->campus_id,
                            'name' => $college->campus_name,
                            'acronym' => $college->campus_acronym
                        ],
                        'undergraduate_programs_count' => (int)$college->undergraduate_programs_count,
                        'graduate_programs_count' => (int)$college->graduate_programs_count,
                        'programs' => (int)$college->undergraduate_programs_count + (int)$college->graduate_programs_count,
                        'files' => 0 // Will be calculated separately if needed
                    ];
                });
        });

        return $this->successResponse($colleges, 'Colleges retrieved successfully');
    }

    /**
     * Store a newly created resource
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'acronym' => 'required|string|max:10|unique:colleges',
            'campus_id' => 'required|exists:campuses,id',
            'logo' => 'sometimes|file|max:5120|mimes:jpg,jpeg,png,gif,svg',
        ]);

        $collegeData = $request->only(['name', 'acronym', 'campus_id']);

        // Handle logo upload if present
        if ($request->hasFile('logo')) {
            $logoInfo = $this->fileService->uploadFile(
                $request->file('logo'),
                'public',
                'college-logos'
            );

            $collegeData = array_merge($collegeData, [
                'logo_path' => $logoInfo['path'],
                'logo_url' => $logoInfo['url'],
                'logo_name' => $logoInfo['name'],
                'logo_type' => $logoInfo['mime_type'],
                'logo_size' => $logoInfo['size'],
            ]);
        }

        $college = College::create($collegeData);
        $college->load('campus'); // Load campus relationship

        // Add computed fields for immediate response
        $college->undergraduate_programs_count = 0;
        $college->graduate_programs_count = 0;
        $college->files = 0;
        $college->programs = 0;

        // Invalidate related caches
        $this->invalidateRelatedCaches();

        return $this->successResponse($college, 'College created successfully', 201);
    }

    /**
     * Display the specified resource
     */
    public function show(College $college): JsonResponse
    {
        $college->load(['campus', 'undergrads', 'graduates']);

        // Add computed counts
        $college->setAttribute('undergraduate_programs_count', $college->undergrads->count());
        $college->setAttribute('graduate_programs_count', $college->graduates->count());
        $college->setAttribute('programs', $college->undergrads->count() + $college->graduates->count());

        return $this->successResponse($college, 'College retrieved successfully');
    }

    /**
     * Update the specified resource
     */
    public function update(Request $request, College $college): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'acronym' => [
                'sometimes',
                'string',
                'max:10',
                Rule::unique('colleges')->ignore($college->getKey())
            ],
            'campus_id' => 'sometimes|exists:campuses,id',
            'logo' => 'sometimes|file|max:5120|mimes:jpg,jpeg,png,gif,svg',
        ]);

        $collegeData = $request->only(['name', 'acronym', 'campus_id']);

        // Handle logo upload if present
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($college->getAttribute('logo_path')) {
                $this->fileService->deleteFile($college->getAttribute('logo_path'), 'public');
            }

            $fileInfo = $this->fileService->uploadFile(
                $request->file('logo'),
                'public',
                'logos/colleges'
            );

            $collegeData['logo_path'] = $fileInfo['path'];
            $collegeData['logo_url'] = $fileInfo['url'];
            $collegeData['logo_name'] = $fileInfo['name'];
            $collegeData['logo_type'] = $fileInfo['mime_type'];
            $collegeData['logo_size'] = $fileInfo['size'];
        }

        $college->update($collegeData);
        $college->load('campus');

        // Invalidate related caches
        $this->invalidateRelatedCaches();

        return $this->successResponse($college, 'College updated successfully');
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(College $college): JsonResponse
    {
        // Check if college has programs
        $programCount = $college->undergrads()->count() + $college->graduates()->count();

        if ($programCount > 0) {
            return $this->errorResponse(
                "Cannot delete college. It has {$programCount} associated programs. Please delete or reassign the programs first.",
                400
            );
        }

        // Delete associated logo if exists
        if ($college->getAttribute('logo_path')) {
            $this->fileService->deleteFile($college->getAttribute('logo_path'), 'public');
        }

        $college->delete();

        // Invalidate related caches
        $this->invalidateRelatedCaches();

        return $this->successResponse(null, 'College deleted successfully');
    }

    /**
     * Force delete college and all associated data
     */
    public function forceDestroy(College $college): JsonResponse
    {
        DB::transaction(function () use ($college) {
            // Delete all curriculum files for this college's programs
            $undergraduateIds = $college->undergrads()->pluck('id');
            $graduateIds = $college->graduates()->pluck('id');

            // Delete curriculum files
            DB::table('curriculum')
                ->where(function($query) use ($undergraduateIds, $graduateIds) {
                    $query->whereIn('program_id', $undergraduateIds)->where('program_type', 'undergrad')
                          ->orWhereIn('program_id', $graduateIds)->where('program_type', 'graduate');
                })
                ->delete();

            // Delete syllabus files
            DB::table('syllabus')
                ->where(function($query) use ($undergraduateIds, $graduateIds) {
                    $query->whereIn('program_id', $undergraduateIds)->where('program_type', 'undergrad')
                          ->orWhereIn('program_id', $graduateIds)->where('program_type', 'graduate');
                })
                ->delete();

            // Delete programs
            $college->undergrads()->delete();
            $college->graduates()->delete();

            // Delete college logo
            if ($college->getAttribute('logo_path')) {
                $this->fileService->deleteFile($college->getAttribute('logo_path'), 'public');
            }

            // Delete college
            $college->delete();
        });

        // Invalidate related caches
        $this->invalidateRelatedCaches();

        return $this->successResponse(null, 'College and all associated data deleted successfully');
    }

    /**
     * Upload logo for college
     */
    public function uploadLogo(Request $request, College $college): JsonResponse
    {
        $request->validate([
            'logo' => 'required|file|max:5120|mimes:jpg,jpeg,png,gif,svg',
        ]);

        // Delete old logo if exists
        if ($college->getAttribute('logo_path')) {
            $this->fileService->deleteFile($college->getAttribute('logo_path'), 'public');
        }

        $fileInfo = $this->fileService->uploadFile(
            $request->file('logo'),
            'public',
            'logos/colleges'
        );

        $college->update([
            'logo_path' => $fileInfo['path'],
            'logo_url' => $fileInfo['url'],
            'logo_name' => $fileInfo['name'],
            'logo_type' => $fileInfo['mime_type'],
            'logo_size' => $fileInfo['size'],
        ]);

        $this->invalidateRelatedCaches();

        return $this->successResponse($college, 'Logo uploaded successfully');
    }

    /**
     * Remove logo from college
     */
    public function removeLogo(College $college): JsonResponse
    {
        if (!$college->getAttribute('logo_path')) {
            return $this->errorResponse('No logo attached to this college', 400);
        }

        $this->fileService->deleteFile($college->getAttribute('logo_path'), 'public');

        $college->update([
            'logo_path' => null,
            'logo_url' => null,
            'logo_name' => null,
            'logo_type' => null,
            'logo_size' => null,
        ]);

        $this->invalidateRelatedCaches();

        return $this->successResponse(null, 'Logo removed successfully');
    }

    /**
     * Invalidate all related caches
     */
    private function invalidateRelatedCaches(): void
    {
        try {
            if (config('cache.default') === 'redis') {
                // Use tags with Redis
                Cache::tags(['colleges', 'dashboard', 'programs', 'files'])->flush();
            } else {
                // Manual key invalidation for other drivers
                $keysToInvalidate = [
                    'colleges_with_counts_v2',
                    'dashboard_data_v6',
                    'dashboard_data_v6_quick',
                    'dashboard_summary_v2'
                ];

                foreach ($keysToInvalidate as $key) {
                    Cache::forget($key);
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Cache invalidation failed', [
                'error' => $e->getMessage(),
                'method' => __METHOD__
            ]);
        }
    }
}
