<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return view('account', ['user' => null, 'applications' => collect()]);
        }

        $applications = Application::where('email', $user->email)
            ->latest()
            ->get();

        return view('account', ['user' => $user, 'applications' => $applications]);
    }
}
