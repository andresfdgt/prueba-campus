<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function list()
    {
        $users = User::where('estado', 1)->get();
        return response()->json($users);
    }
}
