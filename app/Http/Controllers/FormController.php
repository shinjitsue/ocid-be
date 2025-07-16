<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\FileUploadRequest;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FormController extends Controller
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
        $forms = Form::all();
        return $this->successResponse($forms, 'Forms retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'form_number' => 'required|string|max:255|unique:forms',
            'title' => 'required|string|max:255',
            'purpose' => 'required|string',
            'link' => 'required|string|url',
            'revision' => 'required|string|max:255',
            'file' => 'sometimes|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
        ]);

        $formData = $request->only(['form_number', 'title', 'purpose', 'link', 'revision']);

        // Handle file upload if present
        if ($request->hasFile('file')) {
            $fileInfo = $this->fileService->uploadFile(
                $request->file('file'),
                'forms'
            );

            $formData['file_path'] = $fileInfo['path'];
            $formData['file_url'] = $fileInfo['url'];
            $formData['file_name'] = $fileInfo['name'];
            $formData['file_type'] = $fileInfo['mime_type'];
            $formData['file_size'] = $fileInfo['size'];
        }

        $form = Form::create($formData);
        return $this->successResponse($form, 'Form created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Form $form): JsonResponse
    {
        return $this->successResponse($form, 'Form retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Form $form): JsonResponse
    {
        $request->validate([
            'form_number' => 'sometimes|string|max:255|unique:forms,form_number,' . $form->id,
            'title' => 'sometimes|string|max:255',
            'purpose' => 'sometimes|string',
            'link' => 'sometimes|string|url',
            'revision' => 'sometimes|string|max:255',
            'file' => 'sometimes|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
        ]);

        $formData = $request->only(['form_number', 'title', 'purpose', 'link', 'revision']);

        // Handle file upload if present
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($form->file_path) {
                $this->fileService->deleteFile($form->file_path, 'forms');
            }

            $fileInfo = $this->fileService->uploadFile(
                $request->file('file'),
                'forms'
            );

            $formData['file_path'] = $fileInfo['path'];
            $formData['file_url'] = $fileInfo['url'];
            $formData['file_name'] = $fileInfo['name'];
            $formData['file_type'] = $fileInfo['mime_type'];
            $formData['file_size'] = $fileInfo['size'];
        }

        $form->update($formData);
        return $this->successResponse($form, 'Form updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form): JsonResponse
    {
        // Delete associated file if exists
        if ($form->file_path) {
            $this->fileService->deleteFile($form->file_path, 'forms');
        }

        $form->delete();
        return $this->successResponse(null, 'Form deleted successfully');
    }

    /**
     * Upload a file for an existing form
     */
    public function uploadFile(FileUploadRequest $request, Form $form): JsonResponse
    {
        // Delete old file if exists
        if ($form->file_path) {
            $this->fileService->deleteFile($form->file_path, 'forms');
        }

        $fileInfo = $this->fileService->uploadFile(
            $request->file('file'),
            'forms'
        );

        $form->update([
            'file_path' => $fileInfo['path'],
            'file_url' => $fileInfo['url'],
            'file_name' => $fileInfo['name'],
            'file_type' => $fileInfo['mime_type'],
            'file_size' => $fileInfo['size'],
        ]);

        return $this->successResponse($form, 'File uploaded successfully');
    }

    /**
     * Remove the file from a form
     */
    public function removeFile(Form $form): JsonResponse
    {
        if (!$form->file_path) {
            return $this->errorResponse('No file attached to this form', 400);
        }

        $this->fileService->deleteFile($form->file_path, 'forms');

        $form->update([
            'file_path' => null,
            'file_url' => null,
            'file_name' => null,
            'file_type' => null,
            'file_size' => null,
        ]);

        return $this->successResponse(null, 'File removed successfully');
    }
}
