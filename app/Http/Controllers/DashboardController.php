<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard dengan statistik dinamis.
     */
    public function index(Request $request)
    {
        // 1. Statistik Global Sistem (Query Agregat Eloquent Aktual)
        $totalUsers = User::count();
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();
        $draftProjects = Project::where('status', 'draft')->count();
        $archivedProjects = Project::where('status', 'archived')->count();
        $trashedProjects = Project::onlyTrashed()->count();

        // 2. Statistik Pengguna yang Sedang Login
        $user = $request->user();
        $myTotalProjects = $user->projects()->count();
        $myActiveProjects = $user->projects()->where('status', 'active')->count();
        $myDraftProjects = $user->projects()->where('status', 'draft')->count();
        $myTrashedProjects = $user->projects()->onlyTrashed()->count();

        // 3. Proyek Terbaru Pengguna (untuk Quick Overview)
        $recentProjects = $user->projects()
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalUsers',
            'totalProjects',
            'activeProjects',
            'draftProjects',
            'archivedProjects',
            'trashedProjects',
            'myTotalProjects',
            'myActiveProjects',
            'myDraftProjects',
            'myTrashedProjects',
            'recentProjects'
        ));
    }
}
