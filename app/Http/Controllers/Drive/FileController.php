<?php

namespace App\Http\Controllers\Drive;

use App\Http\Controllers\Controller;
use App\Http\Requests\Drive\StoreFileRequest;
use App\Services\Drive\FileService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FileController extends Controller
{
    public function __construct(protected FileService $fileService) {}

    public function index(): JsonResponse
    {
        $files = $this->fileService->getUserFiles(Auth::id());

        return response()->json([
            'status' => 'success',
            'data' => $files,
        ]);
    }

    public function store(StoreFileRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $fileableType = $validated['fileable_type'];

        /** @var Model $fileable */
        $fileable = $fileableType::query()->findOrFail($validated['fileable_id']);

        // The target arrives in the request body, so authorize it after resolution.
        Gate::authorize('attachFile', $fileable);

        $fileRecord = $this->fileService->upload(
            $request->file('file'),
            $request->user(),
            $fileable,
        );

        return response()->json([
            'status' => 'success',
            'message' => 'File uploaded and attached successfully.',
            'data' => $fileRecord,
        ], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->fileService->deleteFile($id, Auth::id());

        return response()->json([
            'status' => 'success',
            'message' => 'File deleted permanently.',
        ]);
    }
}
