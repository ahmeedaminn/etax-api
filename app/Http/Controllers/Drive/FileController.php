<?php

namespace App\Http\Controllers\Drive;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Drive\FileService;
use App\Http\Requests\Drive\StoreFileRequest;

class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    // Fetch all files for the logged-in user
    public function index()
    {
        try {
            $files = $this->fileService->getUserFiles(Auth::id());

            return response()->json([
                'status' => 'success',
                'data'   => $files
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch files.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreFileRequest $request)
    {
        try {
            // 1. Validate the file and ensure the morph targets are provided
            $request->validated();

            // 2. Delegate to the service layer
            $fileRecord = $this->fileService->upload(
                $request->file('file'),
                Auth::id(), // Safely grab the logged-in user's ID
                $request->fileable_id,
                $request->fileable_type
            );

            // 3. Return a clean JSON response
            return response()->json([
                'status'  => 'success',
                'message' => 'File uploaded and attached successfully.',
                'data'    => $fileRecord
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to upload file.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // Delete a specific file
    public function destroy($id)
    {
        try {
            $this->fileService->deleteFile($id, Auth::id());

            return response()->json([
                'status'  => 'success',
                'message' => 'File deleted permanently.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete file.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
