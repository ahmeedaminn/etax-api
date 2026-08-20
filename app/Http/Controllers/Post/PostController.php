<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\ListPostsRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Services\Post\PostService;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(Category $category): JsonResponse
    {
        $posts = $this->postService->getPostsByCategory($category->id);

        return response()->json([
            'status' => 'success',
            'data' => $posts,
        ]);
    }

    public function feed(ListPostsRequest $request): JsonResponse
    {
        $sort = $request->validated('sort', 'latest');

        return response()->json([
            'status' => 'success',
            'data' => $this->postService->getAllPosts($sort),
        ]);
    }

    public function institutionPosts(User $institution): JsonResponse
    {
        $posts = $this->postService->getPostsByInstitution(
            $institution->id
        );

        return response()->json([
            'status' => 'success',
            'data' => $posts,
        ]);
    }

    public function show(Category $category, Post $post): JsonResponse
    {
        $post = $this->postService->getPostById($post->id);

        return response()->json([
            'status' => 'success',
            'data' => $post,
        ]);
    }

    public function store(StorePostRequest $request, Category $category): JsonResponse
    {
        $validatedData = $request->validated();

        $post = $this->postService->createPost($request->user(), $category, $validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully.',
            'data' => $post,
        ], 201);
    }

    public function update(
        UpdatePostRequest $request,
        Category $category,
        Post $post,
    ): JsonResponse {
        $validatedData = $request->validated();

        $updatedPost = $this->postService->updatePost($post, $validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Post updated successfully.',
            'data' => $updatedPost,
        ]);
    }

    public function destroy(Category $category, Post $post): JsonResponse
    {
        $this->postService->deletePost($post);

        return response()->json([
            'status' => 'success',
            'message' => 'Post deleted successfully.',
        ]);
    }
}
