<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Interfaces\Auth\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(protected UserRepositoryInterface $userRepository) {}

    public function getCurrentUserProfile(User $user): User
    {
        return $user->load(['profilePicture', 'institutionProfile.logo']);
    }

    public function register(array $data): array
    {
        // 1. create the user in the database
        $user = $this->userRepository->create($data);

        // 2. Generate the JWT token for this new user
        $token = Auth::guard('api')->login($user);

        // 3. return both token and user to the controller
        return ['user' => $user, 'token' => $token];
    }

    public function login(array $credentials): array
    {
        // 1. Attempt to log in. If it fails, it returns false.
        if (! $token = Auth::guard('api')->attempt($credentials)) {
            // Throw an error that the Controller will catch
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials provided.'],
            ]);
        }

        // 2. If successful, return the user and the new token
        return [
            'user' => Auth::guard('api')->user(),
            'token' => $token,
        ];
    }

    public function logout(): void
    {
        // Invalidates the current token so it can't be used again
        Auth::guard('api')->logout();
    }

    public function refreshToken(): string
    {
        // Issues a brand new token and invalidates the old one
        return Auth::guard('api')->refresh();
    }

    public function sendPasswordResetLink(array $data): string
    {
        // Syntax: Password::sendResetLink() is Laravel's built-in broker method.
        // It takes the array ['email' => '...'] and automatically does the DB lookup and emailing. and make the reset_pass_token table to check on
        // the data coming is just the email
        $status = Password::sendResetLink($data);

        if ($status !== Password::RESET_LINK_SENT) {
            // Syntax: __($status) translates the error string into a readable message.
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }

    public function resetPassword(array $data): string
    {
        // Syntax: Password::reset() takes the data (email, token, password).
        // If the token matches, it runs the "function ($user, $password)" closure.
        $status = Password::reset($data, function ($user, $password) {

            // Syntax: Assign the new password.
            // Because we added 'password' => 'hashed' to our Model casts, Laravel hashes this automatically!
            $user->password = $password;
            $user->save();
        });

        // Syntax: Password::PASSWORD_RESET is the built-in success string.
        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }

    public function updateUserProfile(User $user, array $data): User
    {
        $this->userRepository->updateUserProfile($user, $data);

        return $user->refresh()->load(['profilePicture', 'institutionProfile.logo']);
    }
}
