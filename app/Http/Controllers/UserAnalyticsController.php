<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserAnalyticsController extends Controller
{
    public function newUsers($limit = 10) {
        $users = User::orderBy('created_at','desc')->paginate($limit);

        return $users;
    }
}
