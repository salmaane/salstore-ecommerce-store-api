<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    use ResponseTrait;

    protected function login(Request $request) 
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string']
        ]);

        $user = User::with('socialLinks:user_id,facebook,instagram,twitter,linkedin')->where('email',$request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return $this->error(['message'=> 'email or password are incorrect'], 401);
        }

        if ($user->profile) {
            $user->profile = Storage::disk('public')->url($user->profile);
        }

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('Api Token of '.$user->name, [])->plainTextToken,
            'expiresIn' => 60 * 24
        ], 200);
    }

    protected function register(RegisterUserRequest $request) {
        $request->validated($request);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password'=> Hash::make($request->password),
            'role' => 'user'
        ]);

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('Api Token of '.$user->name, [])->plainTextToken,
            'expiresIn' => 60 * 24
        ], 201);
    }

}
