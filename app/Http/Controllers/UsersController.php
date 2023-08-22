<?php

namespace App\Http\Controllers;

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

        $users = User::where('role', $role)->paginate($limit);

        return $this->success($users, 200);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show(string $id)
    {
        $user = User::find($id);

        if(!$user) {
            return $this->error(["message" => "user not found"], 404);
        }

        return $this->success($user, 200);
    }


    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
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
