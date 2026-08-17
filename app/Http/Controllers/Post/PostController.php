<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Post\PostService;
use App\Http\Requests\Post\StorePostRequest;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    // NEW: Fetch all posts for a specific category
    public function index($categoryId): JsonResponse
    {
        try {
            $posts = $this->postService->getPostsByCategory($categoryId);

            return response()->json([
                'status' => 'success',
                'data'   => $posts
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($postId): JsonResponse
    {
        try {
            $post = $this->postService->getPostById($postId);

            return response()->json([
                'status' => 'success',
                'data'   => $post
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(StorePostRequest $request, $categoryId): JsonResponse
    {

        try {

            $validatedData = $request->validated();

            // 2. Automatically attach the ID of the currently logged-in user
            $validatedData['user_id'] = Auth::id();

            // 3. Automatically attach the category ID from the URL!
            // (This prevents users from tampering with the category_id in the JSON body)
            $validatedData['category_id'] = $categoryId;

            // 4. Pass data to the service layer
            $post = $this->postService->createPost($validatedData);

            // 5. Return success response
            return response()->json([
                'status'  => 'success',
                'message' => 'Post created successfully.',
                'data'    => $post
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create post.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
