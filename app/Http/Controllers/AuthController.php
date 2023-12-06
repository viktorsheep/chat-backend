<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Exception;

class AuthController extends Controller {
    protected $jwt;

    public function __construct(JWTAuth $jwt) {
        $this->jwt = $jwt;

        $this->middleware(
            'auth:api',
            [
                'except' => [
                    'login',
                    'register',
                    'refresh',
                    'logout'
                ]
            ]
        );
    }

    public function register(Request $request) {
        try {
            $user = new User;

            $this->validate($request, [
                'name'      => 'required',
                'email'     => 'required|string|unique:users',
                'password'  => 'required|confirmed',
            ]);

            $user->name = $request->input('name');
            $user->email = $request->email;
            $user->password = app('hash')->make($request->input('password'));
            $user->user_role_id = 3; // client
            $user->save();

            return $this->successResponse($user, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e, 500);
        }
    }

    public function login(Request $request) {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        try {
            if (!$token = $this->jwt->attempt($request->only('email', 'password'))) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['token_absent' => $e->getMessage()], 401);
        }

        $this->jwt->setToken($token);

        return $this->respondWithToken($token);
    }

    public function me() {
        return response()->json($this->jwt->user());
    }

    public function authMe() {
        return response()->json([
            auth()->user()
        ]);
    }

    public function refresh() {
        return $this->respondWithToken($this->jwt->refresh());
    }

    public function logout() {
        try {
            auth()->logout();
            return response()->json(['message' => 'token removed']);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    protected function respondWithToken($token) {
        return response()->json(
            [
                'access_token'  => $token,
                'token_type'    => 'bearer',
                'expires_in'    => $this->jwt->factory()->getTTL() * 60
            ]
        );
    }
}
