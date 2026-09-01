<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $declarationCount = $user->valueDeclarations()->count();

        return view('dashboard', compact('user', 'declarationCount'));
    }
}