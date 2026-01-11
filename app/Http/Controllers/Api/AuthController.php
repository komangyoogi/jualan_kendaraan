<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiFormatter;

use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $params = $request->all();

            $validator = Validator::make($params,
            [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ],
            [
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Email tidak valid',
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 6 karakter',
            ]
        );

        if ($validator->fails())
            return response()->json(ApiFormatter::createJson(400, 'Bad Request', $validator->errors()->all()), 400);
        
        $user = User::where('email', $params['email'])->first();
        if (!$user)
            return response()->json(ApiFormatter::createJson(404, 'User not found'), 404);

        if (!Hash::check($params['password'], $user->password))
            return response()->json(ApiFormatter::createJson(401, 'Unauthorized'), 401);

        if (!$token = JWTAuth::fromUser($user))
            return response()->json(ApiFormatter::createJson(500, 'Unauthorized'), 500);

        $currentDateTime = Carbon::now();
        $expirationDateTime = $currentDateTime->addSeconds(JWTAuth::factory()->getTTL() * 60);

        $info = [
            'type' => 'bearer',
            'token' => $token,
            'expires_at' => $expirationDateTime->format('Y-m-d H:i:s')
        ];

        return response()->json(ApiFormatter::createJson(200, 'Login successful', $info), 200);
        } catch (\Exception $e) {
            return response()->json(ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage()), 500);
        }
    }

    public function me()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $token = JWTAuth::getToken();
        $payload = JWTAuth::getPayload($token);

        $expiration = $payload->get('exp');
        $expiration_time = date('Y-m-d H:i:s', $expiration);

        $data['name'] = $user['name'];
        $data['email'] = $user['email'];
        $data['exp'] = $expiration_time;

        return response()->json(ApiFormatter::createJson(200, 'User profile retrieved successfully', $data), 200);
    }

    public function refresh()
    {
        $currentDateTime = Carbon::now();
        $expirationDateTime = $currentDateTime->addSeconds(JWTAuth::factory()->getTTL() * 60);

        $info = [
            'type' => 'bearer',
            'token' => JWTAuth::refresh(),
            'expires' => $expirationDateTime->format('Y-m-d H:i:s')
        ];

        return response()->json(ApiFormatter::createJson(200, 'Token refreshed successfully', $info), 200);
    }

    public function logout()
    {
        try {
            // Menghapus/Mematikan token agar tidak bisa dipakai lagi
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json(ApiFormatter::createJson(200, 'Logout Successfull'), 200);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            
            return response()->json(ApiFormatter::createJson(500, 'Sorry, the user cannot be logged out'), 500);
        }
    }
}
