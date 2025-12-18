<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\ThematicArea;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $stats = [
            'total_congresses' => Congress::count(),
            'published_congresses' => Congress::where('status', 'published')->count(),
            'draft_congresses' => Congress::where('status', 'draft')->count(),
            'finished_congresses' => Congress::where('status', 'finished')->count(),
            'total_thematic_areas' => ThematicArea::where('is_active', true)->count(),
            'total_users' => User::count(),
        ];

        $recentCongresses = Congress::with('thematicAreas', 'creator')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentCongresses'));
    }
}
