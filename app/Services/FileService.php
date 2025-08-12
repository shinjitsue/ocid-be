<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Upload a file to the specified disk
     *
     * @param UploadedFile $file The uploaded file
     * @param string $disk Storage disk to use
     * @param string|null $folder Optional subfolder
     * @param string|null $filename Optional cust   om filename
     * @return array File information including path and url
     */
    public function uploadFile(UploadedFile $file, string $directory = 'uploads', array $nameComponents = []): array
    {
        // Generate custom filename only for forms directory and when components are provided
        if ($directory === 'forms' && !empty($nameComponents)) {
            $customName = $this->generateCustomFileName($file, $nameComponents);
        } else {
            // Use UUID naming for all other uploads (logos, syllabus, curriculum, etc.)
            $customName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        }

        // Store the file
        $path = $file->storeAs($directory, $customName, 'public');
        
        return [
            'path' => $path,
            'url' => asset('storage/' . $path),
            'name' => $customName,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ];
    }

    private function generateCustomFileName(UploadedFile $file, array $components): string
    {
        $parts = [];
        
        // Add form number (remove spaces and special chars except hyphens)
        if (!empty($components['form_number'])) {
            $parts[] = $this->sanitizeComponent($components['form_number']);
        }
        
        // Add revision (remove spaces, format as Rev.X)
        if (!empty($components['revision'])) {
            $revision = $this->sanitizeComponent($components['revision']);
            // Ensure revision follows Rev.X format
            if (!str_starts_with(strtolower($revision), 'rev')) {
                $revision = 'Rev.' . $revision;
            }
            $parts[] = $revision;
        }
        
        // Generate UUID for uniqueness
        $uuid = Str::uuid();
        $parts[] = $uuid;
        
        // Get file extension
        $extension = $file->getClientOriginalExtension();
        
        // Join parts with hyphens and add extension
        $filename = implode('-', $parts) . '.' . $extension;
        
        return $filename;
    }

    private function sanitizeComponent(string $component): string
    {
        // Remove spaces and convert to string suitable for filename
        $sanitized = preg_replace('/\s+/', '', $component); // Remove all spaces
        $sanitized = preg_replace('/[^a-zA-Z0-9\-\.]/', '', $sanitized); // Keep only alphanumeric, hyphens, and dots
        
        return $sanitized;
    }

    // Add a method specifically for form uploads (for clarity)
    public function uploadFormFile(UploadedFile $file, array $formComponents): array
    {
        return $this->uploadFile($file, 'forms', $formComponents);
    }

    // Add methods for other specific uploads (maintains existing behavior)
    public function uploadLogo(UploadedFile $file): array
    {
        return $this->uploadFile($file, 'logos');
    }

    public function uploadSyllabus(UploadedFile $file): array
    {
        return $this->uploadFile($file, 'syllabus');
    }

    public function uploadCurriculum(UploadedFile $file): array
    {
        return $this->uploadFile($file, 'curriculum');
    }

    public function deleteFile(string $path, string $directory = null): bool
    {
        try {
            Log::info('Attempting to delete file', [
                'path' => $path,
                'full_path' => storage_path('app/public/' . $path),
                'exists' => Storage::disk('public')->exists($path)
            ]);
            
            // Check if file exists before attempting deletion
            if (!Storage::disk('public')->exists($path)) {
                Log::warning('File does not exist for deletion', ['path' => $path]);
                return true; // Consider non-existent file as "successfully deleted"
            }
            
            $result = Storage::disk('public')->delete($path);
            
            if ($result) {
                Log::info('File deleted successfully', [
                    'path' => $path,
                    'still_exists' => Storage::disk('public')->exists($path)
                ]);
            } else {
                Log::error('Storage::delete returned false', ['path' => $path]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('File deletion failed with exception', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
