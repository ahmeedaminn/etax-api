<?php

namespace App\Http\Controllers\Engagement;

use App\Enums\ParticipationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Engagement\SetEventParticipationRequest;
use App\Models\Category;
use App\Models\Post;
use App\Services\Engagement\EventParticipationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventParticipationController extends Controller
{
    public function __construct(
        protected EventParticipationService $participationService
    ) {}

    public function set(
        SetEventParticipationRequest $request,
        Category $category,
        Post $post
    ): JsonResponse {
        $validated = $request->validated();

        $status = ParticipationStatus::from(
            $validated['status']
        );

        $participation = $this->participationService->setParticipation(
            $request->user(),
            $post,
            $status,
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Event participation updated successfully.',
            'data' => $participation,
        ]);
    }

    public function remove(
        Request $request,
        Category $category,
        Post $post
    ): JsonResponse {
        $this->participationService->removeParticipation(
            $request->user(),
            $post,
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Event participation removed successfully.',
        ]);
    }
}
