<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRole;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return view(
            'pages/users', 
            ['roles' => UserRole::orderBy('name', 'desc')->get()->toArray(), 'users' => User::all()],
        );
    }
}
