<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Http\Traits\ApiResponseTrait;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CollegeController extends Controller
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
        $colleges = College::with('campus', 'undergrads', 'graduates')->get();
        return $this->successResponse($colleges, 'Colleges retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'acronym' => 'sometimes|string|max:10|unique:colleges',
            'campus_id' => 'required|exists:campuses,id',
            'logo' => 'sometimes|file|max:5120|mimes:jpg,jpeg,png,gif,svg',
        ]);

        $collegeData = $request->only(['name', 'acronym', 'campus_id']);

        // Handle logo upload if present
        if ($request->hasFile('logo')) {
            $fileInfo = $this->fileService->uploadFile(
                $request->file('logo'),
                'logos/colleges'
            );

            $collegeData['logo_path'] = $fileInfo['path'];
            $collegeData['logo_url'] = $fileInfo['url'];
            $collegeData['logo_name'] = $fileInfo['name'];
            $collegeData['logo_type'] = $fileInfo['mime_type'];
            $collegeData['logo_size'] = $fileInfo['size'];
        }

        $college = College::create($collegeData);
        $college->load('campus');

        return $this->successResponse($college, 'College created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(College $college): JsonResponse
    {
        $college->load('campus', 'undergrads', 'graduates');
        return $this->successResponse($college, 'College retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, College $college): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'acronym' => 'sometimes|string|max:10|unique:colleges,acronym,' . $college->getKey(),
            'campus_id' => 'sometimes|exists:campuses,id',
            'logo' => 'sometimes|file|max:5120|mimes:jpg,jpeg,png,gif,svg',
        ]);

        $collegeData = $request->only(['name', 'acronym', 'campus_id']);

        // Handle logo upload if present
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($college->getAttribute('logo_path')) {
                $this->fileService->deleteFile($college->getAttribute('logo_path'), 'logos/colleges');
            }

            $fileInfo = $this->fileService->uploadFile(
                $request->file('logo'),
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

        return $this->successResponse($college, 'College updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(College $college): JsonResponse
    {
        // Delete associated logo if exists
        if ($college->getAttribute('logo_path')) {
            $this->fileService->deleteFile($college->getAttribute('logo_path'), 'logos/colleges');
        }

        $college->delete();
        return $this->successResponse(null, 'College deleted successfully');
    }

    /**
     * Upload or update college logo
     */
    public function uploadLogo(Request $request, College $college): JsonResponse
    {
        $request->validate([
            'logo' => 'required|file|max:5120|mimes:jpg,jpeg,png,gif,svg',
        ]);

        // Delete old logo if exists
        if ($college->getAttribute('logo_path')) {
            $this->fileService->deleteFile($college->getAttribute('logo_path'), 'logos/colleges');
        }

        $fileInfo = $this->fileService->uploadFile(
            $request->file('logo'),
            'logos/colleges'
        );

        $college->update([
            'logo_path' => $fileInfo['path'],
            'logo_url' => $fileInfo['url'],
            'logo_name' => $fileInfo['name'],
            'logo_type' => $fileInfo['mime_type'],
            'logo_size' => $fileInfo['size'],
        ]);

        return $this->successResponse($college, 'Logo uploaded successfully');
    }

    /**
     * Remove college logo
     */
    public function removeLogo(College $college): JsonResponse
    {
        if (!$college->getAttribute('logo_path')) {
            return $this->errorResponse('No logo attached to this college', 400);
        }

        $this->fileService->deleteFile($college->getAttribute('logo_path'), 'logos/colleges');

        $college->update([
            'logo_path' => null,
            'logo_url' => null,
            'logo_name' => null,
            'logo_type' => null,
            'logo_size' => null,
        ]);

        return $this->successResponse(null, 'Logo removed successfully');
    }
}
