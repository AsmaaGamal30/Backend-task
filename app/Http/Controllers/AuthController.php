<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\VerifyCodeRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    use ApiResponse;

    public function register(UserRegisterRequest $request)
    {
        $verificationCode = $this->generateVerificationCode();
        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => now()->addMinutes(10),
        ]);

        Log::info("User registered with verification code: {$verificationCode}");

        return $this->sendData('User registered successfully', [
            'user' => new UserResource($user),
            'token' => $user->createToken('auth_token')->plainTextToken,
        ], 201);
    }

    public function login(UserLoginRequest $request)
    {
        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials', 401);
        }
        if (!$user->is_verified) {
            return $this->error('Account not verified', 403);
        }
        return $this->sendData('User logged in successfully', [
            'user' => new UserResource($user),
            'token' => $user->createToken('auth_token')->plainTextToken,
        ]);
    }

    public function verifyCode(VerifyCodeRequest $request)
    {

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user || $user->verification_code != $request->verification_code) {
            return $this->error('Invalid verification code.', 400);
        }

        if ($user->verification_code_expires_at < now()) {
            return $this->error('Verification code expired.', 400);
        }

        $user->is_verified = true;
        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendData('Account verified successfully.', [
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    private function generateVerificationCode()
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success('Successfully logged out');
    }
}