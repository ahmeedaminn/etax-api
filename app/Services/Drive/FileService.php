<?php

namespace App\Services\Drive;

use Illuminate\Support\Facades\Storage; // <-- Add it right here!
use Illuminate\Http\UploadedFile;
use App\Repositories\Interfaces\Drive\FileRepositoryInterface;

class FileService
{
    protected $fileRepository;

    public function __construct(FileRepositoryInterface $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function getUserFiles($userId)
    {
        // Fetch all files uploaded by this specific user, newest first
        return \App\Models\File::where('user_id', $userId)->latest()->get();
    }

    public function upload(UploadedFile $file, $userId, $fileableId, $fileableType)
    {
        // 1. Store the physical file in the 'storage/app/public/uploads' folder
        $path = $file->store('uploads', 'public');

        // 2. Format the metadata array to match your Model's $fillable array
        $data = [
            'user_id'       => $userId,
            'file_path'     => $path,
            'file_name'     => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size_in_kb'    => round($file->getSize() / 1024),
            'fileable_id'   => $fileableId,
            'fileable_type' => $fileableType,
        ];

        // 3. Save to database via repository
        return $this->fileRepository->create($data);
    }

    public function deleteFile($fileId, $userId)
    {
        // 1. Find the file, but ONLY if it belongs to the logged-in user (Security!)
        $file = \App\Models\File::where('id', $fileId)
            ->where('user_id', $userId)
            ->firstOrFail();

        // 2. Delete the physical file from the 'storage/app/public' folder
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        // 3. Delete the record from the database
        return $file->delete();
    }
}
