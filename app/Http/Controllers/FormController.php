<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\FileUploadRequest;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
        $forms = Form::orderBy('form_number')->get();
        return $this->successResponse($forms, 'Forms retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request): JsonResponse
{
    try {
        $request->validate([
            'form_number' => 'required|string|max:255|unique:forms',
            'title' => 'required|string|max:255',
            'purpose' => 'required|string',
            'link' => 'nullable|string|url|max:2048',
            'revision' => 'nullable|string|max:255',
            'file' => 'sometimes|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
        ]);

        $formData = $request->only(['form_number', 'title', 'purpose', 'link', 'revision']);

        // Handle file upload if present - ONLY FORMS GET CUSTOM NAMING
        if ($request->hasFile('file')) {
            // Use the specialized form upload method
            $nameComponents = [
                'form_number' => $request->input('form_number'),
                'revision' => $request->input('revision'),
            ];

            $fileInfo = $this->fileService->uploadFormFile(
                $request->file('file'),
                $nameComponents
            );

            $formData['file_path'] = $fileInfo['path'];
            $formData['file_url'] = $fileInfo['url'];
            $formData['file_name'] = $fileInfo['name'];
            $formData['file_type'] = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
            $formData['file_size'] = $fileInfo['size'];
        }

        $form = Form::create($formData);
        
        // Invalidate related caches
        $this->invalidateRelatedCaches();
        
        Log::info('Form created successfully', ['form_id' => $form->id, 'form_number' => $form->form_number]);
        
        return $this->successResponse($form, 'Form created successfully', 201);
    } catch (ValidationException $e) {
        Log::error('Form creation validation failed', ['errors' => $e->errors()]);
        throw $e;
    } catch (\Exception $e) {
        Log::error('Form creation failed', ['error' => $e->getMessage()]);
        return $this->errorResponse('Failed to create form: ' . $e->getMessage(), 500);
    }
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
        try {
            Log::info('Form update attempt', [
                'form_id' => $form->id,
                'request_data' => $request->except(['file']),
                'has_file' => $request->hasFile('file'),
                'remove_file' => $request->input('remove_file'),
                'content_type' => $request->header('Content-Type'),
                'current_file_path' => $form->file_path
            ]);

            // Validation rules
            $rules = [
                'form_number' => 'sometimes|required|string|max:255|unique:forms,form_number,' . $form->id,
                'title' => 'sometimes|required|string|max:255',
                'purpose' => 'sometimes|required|string',
                'link' => 'nullable|string|url|max:2048',
                'revision' => 'nullable|string|max:255',
                'file' => 'sometimes|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
                'remove_file' => 'sometimes|string|in:true,false',
            ];

            $validator = validator($request->all(), $rules);

            if ($validator->fails()) {
                Log::error('Form update validation failed', ['errors' => $validator->errors()]);
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            // Get validated data
            $formData = $request->only(['form_number', 'title', 'purpose', 'link', 'revision']);
            
            // Remove empty values to avoid overwriting with nulls
            $formData = array_filter($formData, function($value) {
                return $value !== null && $value !== '';
            });

            Log::info('Form data to update', ['form_data' => $formData]);

            // Handle file operations
            if ($request->hasFile('file')) {
                Log::info('Processing new file upload for form update');
                
                // Delete old file if exists
                if ($form->file_path) {
                    Log::info('Deleting old file before uploading new one', ['old_file_path' => $form->file_path]);
                    $deleted = $this->fileService->deleteFile($form->file_path);
                    if ($deleted) {
                        Log::info('Old file deleted successfully', ['file_path' => $form->file_path]);
                    } else {
                        Log::warning('Failed to delete old file, continuing with upload', ['file_path' => $form->file_path]);
                    }
                }

                // Upload new file with custom naming
                $nameComponents = [
                    'form_number' => $request->input('form_number', $form->form_number),
                    'revision' => $request->input('revision', $form->revision),
                ];

                $fileInfo = $this->fileService->uploadFormFile(
                    $request->file('file'),
                    $nameComponents
                );

                $formData['file_path'] = $fileInfo['path'];
                $formData['file_url'] = $fileInfo['url'];
                $formData['file_name'] = $fileInfo['name'];
                $formData['file_type'] = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
                $formData['file_size'] = $fileInfo['size'];
                
                Log::info('New file uploaded successfully', [
                    'new_file_path' => $fileInfo['path'],
                    'new_file_name' => $fileInfo['name']
                ]);
                
            } elseif ($request->input('remove_file') === 'true') {
                Log::info('Processing file removal for form update');
                
                // Delete old file if exists
                if ($form->file_path) {
                    $deleted = $this->fileService->deleteFile($form->file_path);
                    if ($deleted) {
                        Log::info('File removed successfully', ['file_path' => $form->file_path]);
                    } else {
                        Log::warning('Failed to remove file', ['file_path' => $form->file_path]);
                    }
                }

                // Clear file fields
                $formData['file_path'] = null;
                $formData['file_url'] = null;
                $formData['file_name'] = null;
                $formData['file_type'] = null;
                $formData['file_size'] = null;
            }

            // Update the form
            $updated = $form->update($formData);
            
            if (!$updated) {
                Log::error('Form update returned false', ['form_id' => $form->id]);
                return $this->errorResponse('Failed to update form in database', 500);
            }

            // Refresh the model to get updated data
            $form->refresh();
            
            // Invalidate related caches
            $this->invalidateRelatedCaches();
            
            Log::info('Form updated successfully', [
                'form_id' => $form->id,
                'updated_fields' => array_keys($formData),
                'final_file_path' => $form->file_path
            ]);
            
            return $this->successResponse($form, 'Form updated successfully');
            
        } catch (ValidationException $e) {
            Log::error('Form update validation exception', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Form update failed with exception', [
                'form_id' => $form->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Failed to update form: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form): JsonResponse
    {
        try {
            // Delete associated file if exists
            if ($form->file_path) {
                $this->fileService->deleteFile($form->file_path, 'forms');
            }

            $form->delete();
            
            // Invalidate related caches
            $this->invalidateRelatedCaches();
            
            Log::info('Form deleted successfully', ['form_id' => $form->id]);
            
            return $this->successResponse(null, 'Form deleted successfully');
        } catch (\Exception $e) {
            Log::error('Form deletion failed', [
                'form_id' => $form->id,
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse('Failed to delete form: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Upload a file for an existing form
     */
    public function uploadFile(FileUploadRequest $request, Form $form): JsonResponse
{
    try {
        // Delete old file if exists
        if ($form->file_path) {
            $this->fileService->deleteFile($form->file_path, 'forms');
        }

        // Use the specialized form upload method with custom naming
        $nameComponents = [
            'form_number' => $form->form_number,
            'revision' => $form->revision,
        ];

        $fileInfo = $this->fileService->uploadFormFile(
            $request->file('file'),
            $nameComponents
        );

        $form->update([
            'file_path' => $fileInfo['path'],
            'file_url' => $fileInfo['url'],
            'file_name' => $fileInfo['name'],
            'file_type' => pathinfo($fileInfo['name'], PATHINFO_EXTENSION),
            'file_size' => $fileInfo['size'],
        ]);

        // Invalidate related caches
        $this->invalidateRelatedCaches();

        return $this->successResponse($form, 'File uploaded successfully');
    } catch (\Exception $e) {
        Log::error('File upload failed', [
            'form_id' => $form->id,
            'error' => $e->getMessage()
        ]);
        return $this->errorResponse('Failed to upload file: ' . $e->getMessage(), 500);
    }
}

    /**
     * Remove the file from a form
     */
    public function removeFile(Form $form): JsonResponse
    {
        try {
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

            // Invalidate related caches
            $this->invalidateRelatedCaches();

            return $this->successResponse($form, 'File removed successfully');
        } catch (\Exception $e) {
            Log::error('File removal failed', [
                'form_id' => $form->id,
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse('Failed to remove file: ' . $e->getMessage(), 500);
        }
    }

    private function invalidateRelatedCaches(): void
    {
        try {
            if (config('cache.default') === 'redis') {
                // Use tags with Redis
                Cache::tags(['dashboard', 'forms'])->flush();
            } else {
                // Manual key invalidation for other drivers
                $keysToInvalidate = [
                    'dashboard_data_v6',
                    'dashboard_data_v6_quick',
                    'dashboard_summary_v2'
                ];

                foreach ($keysToInvalidate as $key) {
                    Cache::forget($key);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Cache invalidation failed', [
                'error' => $e->getMessage(),
                'method' => __METHOD__
            ]);
        }
    }
}