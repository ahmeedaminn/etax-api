<?php

namespace App\Http\Controllers\Engagement;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\Engagement\SavedPostService;


class SavedPostController extends Controller
{
    public function __construct(
        protected SavedPostService $savedPostService
    ) {}

    public function save(Request $request, Category $category, Post $post)
    {
        $this->savedPostService->savePost($request->user(), $post);

        return response()->json([
            'status' => 'success',
            'message' => 'Post saved successfully.',
        ]);
    }

    public function unsave(Request $request, Category $category, Post $post)
    {
        $this->savedPostService->unsavePost($request->user(), $post);

        return response()->json([
            'status' => 'success',
            'message' => 'Post unsaved successfully.',
        ]);
    }
}
