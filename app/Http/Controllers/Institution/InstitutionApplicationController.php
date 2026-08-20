<?php

namespace App\Http\Controllers\Institution;

use App\Enums\InstitutionRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Institution\ReviewInstitutionApplicationRequest;
use App\Http\Requests\Institution\StoreInstitutionApplicationRequest;
use App\Http\Requests\Institution\UpdateInstitutionProfileRequest;
use App\Models\User;
use App\Services\Institution\InstitutionApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstitutionApplicationController extends Controller
{
    public function __construct(
        protected InstitutionApplicationService $institutionApplicationService,
    ) {}

    public function store(StoreInstitutionApplicationRequest $request): JsonResponse
    {
        $applicant = $this->institutionApplicationService->submitApplication(
            $request->user(),
            $request->validated(),
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Institution application submitted successfully.',
            'data' => $applicant,
        ], 201);
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->institutionApplicationService->getApplication(
                $request->user(),
            ),
        ]);
    }

    public function update(UpdateInstitutionProfileRequest $request): JsonResponse
    {
        $applicant = $this->institutionApplicationService->updateApplication(
            $request->user(),
            $request->validated(),
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Institution profile updated successfully.',
            'data' => $applicant,
        ]);
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->institutionApplicationService->getPendingApplications(),
        ]);
    }

    public function review(
        ReviewInstitutionApplicationRequest $request,
        User $applicant,
    ): JsonResponse {
        $reviewedApplicant = $this->institutionApplicationService->reviewApplication(
            $applicant,
            InstitutionRequestStatus::from($request->validated('status')),
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Institution application reviewed successfully.',
            'data' => $reviewedApplicant,
        ]);
    }
}
