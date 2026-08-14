<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function userDashboard()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login');
        }
        
        return view('user.dashboard', compact('user'));
    }

    public function adminDashboard()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login');
        }
        
        return view('admin.dashboard', compact('user'));
    }

    public function superAdminDashboard()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login');
        }
        
        return view('super-admin.dashboard', compact('user'));
    }
}