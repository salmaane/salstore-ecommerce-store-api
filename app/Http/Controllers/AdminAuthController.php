<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminAuthController extends Controller
{
    use ResponseTrait;

    protected function login(Request $request) {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string']
        ]);

        $user = User::with('socialLinks:user_id,facebook,instagram,twitter,linkedin')->where('email', $request->email)->first();
        
        if(!$user || !Hash::check($request->password, $user->password) || $user->role != 'admin') {
            return $this->error(['message' => 'email or password are incorrect'], 401);
        }

        if ($user->profile) {
            $user->profile = Storage::disk('public')->url($user->profile);
        }

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('Api Token of admin '.$user->name, ['admin'])->plainTextToken,
            'expiresIn' => 60*24*7
        ], 200);
    }
}
