<?php

namespace App\Http\Controllers;

use App\Models\Syllabus;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\FileUploadRequest;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class SyllabusController extends Controller
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
        $syllabi = Syllabus::with(['graduateProgram.college', 'undergradProgram.college'])->get();
        return $this->successResponse($syllabi, 'Syllabi retrieved successfully');
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

        // Validate that the program exists
        $this->validateProgramExists($request->program_id, $request->program_type);

        $syllabusData = $request->only(['program_id', 'program_type', 'image_url']);

        // Handle file upload if present
        if ($request->hasFile('file')) {
            $fileInfo = $this->fileService->uploadFile(
                $request->file('file'),
                'syllabus'
            );

            $syllabusData['file_path'] = $fileInfo['path'];
            $syllabusData['file_url'] = $fileInfo['url'];
            $syllabusData['file_name'] = $fileInfo['name'];
            $syllabusData['file_type'] = $fileInfo['mime_type'];
            $syllabusData['file_size'] = $fileInfo['size'];
        }

        $syllabus = Syllabus::create($syllabusData);
        return $this->successResponse($syllabus, 'Syllabus created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Syllabus $syllabus): JsonResponse
    {
        $syllabus->load(['graduateProgram.college', 'undergradProgram.college']);
        return $this->successResponse($syllabus, 'Syllabus retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Syllabus $syllabus): JsonResponse
    {
        $request->validate([
            'image_url' => 'sometimes|string|url',
            'program_id' => 'sometimes|integer',
            'program_type' => 'sometimes|in:graduate,undergrad',
            'file' => 'sometimes|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
        ]);

        if ($request->has(['program_id', 'program_type'])) {
            $this->validateProgramExists($request->program_id, $request->program_type);
        }

        $syllabusData = $request->only(['image_url', 'program_id', 'program_type']);

        // Handle file upload if present
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($syllabus->file_path) {
                $this->fileService->deleteFile($syllabus->file_path, 'syllabus');
            }

            $fileInfo = $this->fileService->uploadFile(
                $request->file('file'),
                'syllabus'
            );

            $syllabusData['file_path'] = $fileInfo['path'];
            $syllabusData['file_url'] = $fileInfo['url'];
            $syllabusData['file_name'] = $fileInfo['name'];
            $syllabusData['file_type'] = $fileInfo['mime_type'];
            $syllabusData['file_size'] = $fileInfo['size'];
        }

        $syllabus->update($syllabusData);
        return $this->successResponse($syllabus, 'Syllabus updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Syllabus $syllabus): JsonResponse
    {
        // Delete associated file if exists
        if ($syllabus->file_path) {
            $this->fileService->deleteFile($syllabus->file_path, 'syllabus');
        }

        $syllabus->delete();
        return $this->successResponse(null, 'Syllabus deleted successfully');
    }

    /**
     * Upload a file for an existing syllabus
     */
    public function uploadFile(FileUploadRequest $request, Syllabus $syllabus): JsonResponse
    {
        // Delete old file if exists
        if ($syllabus->file_path) {
            $this->fileService->deleteFile($syllabus->file_path, 'syllabus');
        }

        $fileInfo = $this->fileService->uploadFile(
            $request->file('file'),
            'syllabus'
        );

        $syllabus->update([
            'file_path' => $fileInfo['path'],
            'file_url' => $fileInfo['url'],
            'file_name' => $fileInfo['name'],
            'file_type' => $fileInfo['mime_type'],
            'file_size' => $fileInfo['size'],
        ]);

        return $this->successResponse($syllabus, 'File uploaded successfully');
    }

    /**
     * Remove the file from a syllabus
     */
    public function removeFile(Syllabus $syllabus): JsonResponse
    {
        if (!$syllabus->file_path) {
            return $this->errorResponse('No file attached to this syllabus', 400);
        }

        $this->fileService->deleteFile($syllabus->file_path, 'syllabus');

        $syllabus->update([
            'file_path' => null,
            'file_url' => null,
            'file_name' => null,
            'file_type' => null,
            'file_size' => null,
        ]);

        return $this->successResponse(null, 'File removed successfully');
    }

    /**
     * Validate that the program exists.
     */
    private function validateProgramExists($programId, $programType)
    {
        $model = $programType === 'graduate' ? \App\Models\Graduate::class : \App\Models\Undergrad::class;

        if (!$model::find($programId)) {
            $validator = Validator::make([], []);
            $validator->errors()->add('program_id', 'The selected program does not exist.');

            throw new ValidationException($validator);
        }
    }
}
