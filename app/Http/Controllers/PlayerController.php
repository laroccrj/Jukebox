<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;

class PlayerController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(UserService $userService)
    {
        $user = $userService->getUserFromSession();
        return view('player', [
          'user' => print_r($user, true)
        ]);
    }
}
