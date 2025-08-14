<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\FileUploadRequest;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CurriculumController extends Controller
{
    use ApiResponseTrait;

    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            // Get all curriculum without loading relationships that might cause issues
            $curricula = Curriculum::select([
                'id', 
                'program_id', 
                'program_type', 
                'file_path', 
                'file_url', 
                'file_name', 
                'file_type', 
                'file_size', 
                'created_at',
                'updated_at'
            ])->get();

            Log::info('Curricula fetched successfully', ['count' => $curricula->count()]);
            
            return $this->successResponse($curricula, 'Curricula retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error fetching curricula', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse('Failed to fetch curricula: ' . $e->getMessage(), 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'program_id' => 'required|integer',
            'program_type' => 'required|in:graduate,undergrad',
            'file' => 'sometimes|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
        ]);

        $this->validateProgramExists($request->input('program_id'), $request->input('program_type'));

        $curriculumData = $request->only(['program_id', 'program_type']);

        // Handle file upload if present
        if ($request->hasFile('file')) {
            $fileInfo = $this->fileService->uploadFile(
                $request->file('file'),
                'curriculum'
            );

            $curriculumData['file_path'] = $fileInfo['path'];
            $curriculumData['file_url'] = $fileInfo['url'];
            $curriculumData['file_name'] = $fileInfo['name'];
            $curriculumData['file_type'] = $fileInfo['mime_type'];
            $curriculumData['file_size'] = $fileInfo['size'];
        }

        $curriculum = Curriculum::create($curriculumData);
        return $this->successResponse($curriculum, 'Curriculum created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Curriculum $curriculum): JsonResponse
    {
        $curriculum->load(['graduateProgram.college', 'undergradProgram.college']);
        return $this->successResponse($curriculum, 'Curriculum retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Curriculum $curriculum): JsonResponse
    {
        $request->validate([
            'program_id' => 'sometimes|integer',
            'program_type' => 'sometimes|in:graduate,undergrad',
            'file' => 'sometimes|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
        ]);

        // Only validate program exists if both program_id and program_type are being updated
        if ($request->has('program_id') && $request->has('program_type')) {
            $this->validateProgramExists($request->input('program_id'), $request->input('program_type'));
        }

        $curriculumData = $request->only(['program_id', 'program_type']);

        // Handle file upload if present
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($curriculum->file_path) {
                $this->fileService->deleteFile($curriculum->file_path, 'curriculum');
            }

            $fileInfo = $this->fileService->uploadFile(
                $request->file('file'),
                'curriculum'
            );

            $curriculumData['file_path'] = $fileInfo['path'];
            $curriculumData['file_url'] = $fileInfo['url'];
            $curriculumData['file_name'] = $fileInfo['name'];
            $curriculumData['file_type'] = $fileInfo['mime_type'];
            $curriculumData['file_size'] = $fileInfo['size'];
        }

        $curriculum->update($curriculumData);
        return $this->successResponse($curriculum, 'Curriculum updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Curriculum $curriculum): JsonResponse
    {
        try {
            // Delete associated file if exists
            if ($curriculum->file_path) {
                $this->fileService->deleteFile($curriculum->file_path, 'curriculum');
            }

            $curriculum->delete();
            return $this->successResponse(null, 'Curriculum deleted successfully');
        } catch (\Exception $e) {
            Log::error('Curriculum deletion failed', [
                'curriculum_id' => $curriculum->id,
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse('Failed to delete curriculum: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Upload a file for an existing curriculum
     */
    public function uploadFile(FileUploadRequest $request, Curriculum $curriculum): JsonResponse
    {
        // Delete old file if exists
        if ($curriculum->getAttribute('file_path')) {
            $this->fileService->deleteFile($curriculum->getAttribute('file_path'), 'curriculum');
        }

        $fileInfo = $this->fileService->uploadFile(
            $request->file('file'),
            'curriculum'
        );

        $curriculum->update([
            'file_path' => $fileInfo['path'],
            'file_url' => $fileInfo['url'],
            'file_name' => $fileInfo['name'],
            'file_type' => $fileInfo['mime_type'],
            'file_size' => $fileInfo['size'],
        ]);

        return $this->successResponse($curriculum, 'File uploaded successfully');
    }

    /**
     * Remove the file from a curriculum
     */
    public function removeFile(Curriculum $curriculum): JsonResponse
    {
        if (!$curriculum->getAttribute('file_path')) {
            return $this->errorResponse('No file attached to this curriculum', 400);
        }

        $this->fileService->deleteFile($curriculum->getAttribute('file_path'), 'curriculum');

        $curriculum->update([
            'file_path' => null,
            'file_url' => null,
            'file_name' => null,
            'file_type' => null,
            'file_size' => null,
        ]);

        return $this->successResponse(null, 'File removed successfully');
    }

    /**
     * Validate that the program exists
     */
    private function validateProgramExists($programId, $programType)
    {
        $model = $programType === 'graduate' ? \App\Models\Graduate::class : \App\Models\Undergrad::class;

        if (!$model::where('id', $programId)->exists()) {
            throw ValidationException::withMessages([
                'program_id' => ["The selected {$programType} program does not exist."]
            ]);
        }
    }
}
