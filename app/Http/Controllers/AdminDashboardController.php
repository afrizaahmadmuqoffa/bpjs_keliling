<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParticipantModel;
use App\Models\RegionModel;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
