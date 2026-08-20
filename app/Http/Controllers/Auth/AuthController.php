<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendPasswordResetLinkRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\UpdateProfileRequest;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'User successfully created.',
            'user' => $result['user'],
            'token' => $result['token'],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'message' => 'Login successful',
            'user' => $result['user'],
            'token' => $result['token'],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->getCurrentUserProfile($request->user());

        return response()->json($user);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refreshToken(): JsonResponse
    {
        $newToken = $this->authService->refreshToken();

        return response()->json([
            'message' => 'Token refreshed',
            'token' => $newToken,
        ]);
    }

    public function sendPasswordResetLink(SendPasswordResetLinkRequest $request): JsonResponse
    {
        $message = $this->authService->sendPasswordResetLink($request->validated());

        return response()->json(['message' => $message]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $message = $this->authService->resetPassword($request->validated());

        return response()->json(['message' => $message]);
    }

    public function updateUserProfile(UpdateProfileRequest $request): JsonResponse
    {

        $updatedUser = $this->authService->updateUserProfile(
            $request->user(),
            $request->validated(),
        );

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $updatedUser,
        ]);
    }
}
