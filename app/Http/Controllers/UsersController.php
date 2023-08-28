<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $limit = $request->limit ?? 10;
        $role = $request->role ?? 'user';

        $users = User::with('socialLinks:user_id,facebook,instagram,twitter,linkedin')->where('role', $role)->paginate($limit);

        return $this->success($users, 200);
    }


    public function show(string $id)
    {
        $user = User::with('socialLinks:user_id,facebook,instagram,twitter,linkedin')->find($id);

        if(!$user) {
            return $this->error(["message" => "user not found"], 404);
        }

        return $this->success($user, 200);
    }


    public function update(UpdateUserRequest $request, string $id)
    {
        $request->validated($request->all());

        $user = User::find($id);
        if (!$user) {
            return $this->error(['message' => "user (id: $id) not found"], 404);
        }

        $user->update($request->except(['facebook', 'instagram', 'twitter', 'linkedin']));
        $user->socialLinks()->update($request->only(['facebook','instagram','twitter','linkedin']));
        
        $user = User::with('socialLinks:user_id,facebook,instagram,twitter,linkedin')->find($id);
        return $this->success([$user], 200);
    }


    public function destroy(string $id)
    {
        if(!User::destroy($id)) {
            return $this->error(["message" => "User with id: $id not found"],404);
        }

        return $this->success([
            "message" => "User deleted successfully."
        ], 200);
    }
}
