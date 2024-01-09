<?php

namespace App\Controllers;

use Framework\Http\Request;

use Framework\Support\Facades\Auth;
use Framework\Support\Facades\Hash;

use App\Models\User;

/**
 * Class UserController
 *
 * Handles user-related actions such as registration, login, logout, and fetching user data.
 */
class UserController extends Controller
{
    /**
     * Fetch the authenticated user's data.
     *
     * @param Request $request
     * @return \Framework\Http\Response
     */
    public function user(Request $request)
    {
        return $this->successResponse("User data fetched successfully.", ['user' => $request->user()]);
    }

    /**
     * Register a new user.
     *
     * @param Request $request
     * @return \Framework\Http\Response
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string|confirmed'
        ], [], [
            'login' => 'username'
        ]);

        if (!$user = User::create(['login' => $validated['login'], 'password' => Hash::make($validated['password'])])) return $this->errorResponse("Failed to create new account. Please try again later.", 401);

        $token = $user->createToken();

        return $this->successResponse("Registration successful.", ['user' => $user, 'token' => $token->plainTextToken]);
    }

    /**
     * Log in an existing user.
     *
     * @param Request $request
     * @return \Framework\Http\Response
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt(['login' => $validated['login'], 'password' => $validated['password']])) return $this->errorResponse("Invalid username or password. Please check your credentials.", 401);

        $user = Auth::user();
        $token = $user->createToken();

        return $this->successResponse("Login successful.", ['user' => $user, 'token' => $token->plainTextToken]);
    }

    /**
     * Log out the authenticated user.
     *
     * @param Request $request
     * @return \Framework\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse("Logout successful.");
    }
}