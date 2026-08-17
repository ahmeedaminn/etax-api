<?php

namespace App\Http\Controllers\Auth;


use App\Services\Auth\AuthService;
use GuzzleHttp\Psr7\Response;
use App\Http\Controllers\Controller; // <-- THIS FIXES THE ERROR
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SendPasswordResetLinkRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;


class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {

        try {
            // 2. Validate the incoming HTTP request
            $validatedData = $request->validated();

            // 3. Hand the validated array to the Service
            $result = $this->authService->register($validatedData);

            // 3. return the JSON response to the client
            return response()->json([
                'message' => 'User successfully Created.',
                "user" => $result['user'],
                "token" => $result["token"]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to register user.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function login(LoginRequest $request): JsonResponse
    {

        try {
            $credentials = $request->validated();

            $result = $this->authService->login($credentials);

            return response()->json([
                'message' => 'Login successful',
                'user'    => $result['user'],
                'token'   => $result['token'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to login.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function me(): JsonResponse
    {
        // Returns the currently authenticated user's profile
        return response()->json(auth('api')->user());
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function refreshToken(): JsonResponse
    {
        $newToken = $this->authService->refreshToken();

        return response()->json([
            'message' => 'Token refreshed',
            'token'   => $newToken,
        ]);
    }

    public function sendPasswordResetLink(SendPasswordResetLinkRequest $request): JsonResponse
    {
        try {
            // Syntax: Validate that the request has an email field.
            $request->validated();

            // Syntax: $request->only('email') extracts just the email as an array ['email' => '...']
            $message = $this->authService->sendPasswordResetLink($request->only('email'));

            return response()->json(['message' => $message]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to send the password reset link.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {

        try {
            // run the validations
            // Syntax: 'confirmed' is a special Laravel rule. 
            // It automatically checks if 'password' matches a field named 'password_confirmation' sent from React.
            $data = $request->validated();

            $message = $this->authService->resetPassword($data);

            return response()->json(['message' => $message]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to reset the password.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
