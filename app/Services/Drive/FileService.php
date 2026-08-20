<?php

namespace App\Services\Drive;

use App\Models\File;
use App\Models\User;
use App\Repositories\Interfaces\Drive\FileRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    protected $fileRepository;

    public function __construct(FileRepositoryInterface $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function getUserFiles(int $userId): Collection
    {
        return File::query()->where('user_id', $userId)->latest()->get();
    }

    public function upload(UploadedFile $file, User $uploader, Model $fileable): File
    {
        $path = $file->store('uploads', 'public');

        $data = [
            'user_id' => $uploader->id,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size_in_kb' => round($file->getSize() / 1024),
            'fileable_id' => $fileable->getKey(),
            'fileable_type' => $fileable->getMorphClass(),
        ];

        return $this->fileRepository->create($data);
    }

    public function deleteFile(int $fileId, int $userId): bool
    {
        $file = File::query()
            ->where('id', $fileId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        return (bool) $file->delete();
    }
}
