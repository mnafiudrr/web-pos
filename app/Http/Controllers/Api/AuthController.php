<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|unique:users',
            'fullname' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'shop_name' => 'required|string',
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);

        DB::beginTransaction();
        try {
            $user = User::create($validatedData);
    
            $profile = $user->profile()->create([
                'fullname' => $validatedData['fullname'],
                'nickname' => explode(' ', $validatedData['fullname'])[0],
            ]);
    
            $shop = Shop::create([
                'name' => $validatedData['shop_name'],
                'owner' => $user->id,
                'created_by' => $user->id,
            ]);
    
            $user->update([
                'shop_id' => $shop->id,
            ]);

            DB::commit();

            $plainTextToken = $user->createToken('authToken')->plainTextToken;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response(['message' => 'Failed to register user', 'data' => $th], 500);
        }

        $user->fullname = $profile->fullname;
        $user->nickname = $profile->nickname;

        return response([
            'message' => 'Successfully registered user',
            'data' => [
                'user' => $user->load('profile'),
                'shop' => $shop,
                'token' => $plainTextToken
            ]
        ]);
    }

    /**
     * Login an existing user.
     */
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid credentials'], 401);
        }

        $user = User::where('username', $loginData['username'])->first();

        $token = $user->createToken('authToken')->plainTextToken;

        return response([
            'message' => 'Successfully logged in',
            'data' => [
                'user' => $user->load('profile'),
                'shop' => auth()->user()->shop,
                'token' => $token
            ]
        ]);
    }


    /**
     * check token
     */
    public function checkToken(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->first();

        return response([
            'message' => 'Successfully logged in',
            'data' => [
                // user wiith profile
                'user' => $user->load('profile'),
                'shop' => $user->shop,
            ]
        ]);
    }

    /**
     * Logout an existing user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response(['message' => 'Successfully logged out']);
    }
}
