<?php

namespace App\Http\Controllers;

use App\Models\CapitalAccess;
use App\Models\Exchange;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $stats = [
            'total_users' => User::where('role', 'individual')->count(),
            'active_exchanges' => Exchange::where('status', 'active')->count(),
            'disputed_exchanges' => Exchange::where('status', 'disputed')->count(),
            'capital_applications' => CapitalAccess::count(),
        ];

        $registrationsByMonth = User::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')->orderBy('month')->pluck('count', 'month');

        return view('admin.dashboard', compact('stats', 'registrationsByMonth'));
    }
}