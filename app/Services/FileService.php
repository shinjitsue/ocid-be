<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
    public function uploadFile(UploadedFile $file, string $disk, ?string $folder = null, ?string $filename = null): array
    {
        // Generate a unique filename if not provided
        if (!$filename) {
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;
        }

        // Prepare the path where the file will be stored
        $path = $folder ? "$folder/$filename" : $filename;

        // Store the file on the specified disk
        $storedPath = $file->storeAs('', $path, $disk);

        // Get the URL using method if available, otherwise build manually
        $storage = Storage::disk($disk);
        if (method_exists($storage, 'url')) {
            $url = $storage->url($storedPath);
        } else {
            $url = config('app.url') . '/storage/' . $storedPath;
        }

        return [
            'path' => $storedPath,
            'url' => $url,
            'name' => $filename,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    /**
     * Delete a file from storage
     */
    public function deleteFile(string $path, string $disk): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }
}
