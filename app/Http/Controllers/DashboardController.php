<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendancePoint;
use App\Models\Logbook;
use App\Models\Projek;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard.
     */
    public function admin(): View
    {
        $stats = [
            'total_karyawan' => User::where('role', 'karyawan')->count(),
            'total_projek' => Projek::where('status', 'aktif')->count(),
            'presensi_hari_ini' => Attendance::whereDate('date', now()->toDateString())->count(),
            'titik_gps_aktif' => AttendancePoint::where('is_active', true)->count(),
        ];

        $recentAttendances = Attendance::with(['user', 'attendancePoint'])
            ->whereDate('date', now()->toDateString())
            ->latest()
            ->take(5)
            ->get();

        $activeProjects = Projek::withCount(['users', 'attendancePoints'])
            ->where('status', 'aktif')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.index', compact('stats', 'recentAttendances', 'activeProjects'));
    }

    /**
     * Display the Employee / Karyawan Dashboard.
     */
    public function karyawan(): View
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $todayAttendance = Attendance::with('attendancePoint')
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $assignedProjects = $user->projeks()
            ->with('attendancePoints')
            ->where('status', 'aktif')
            ->get();

        $recentLogbooks = $user->logbooks()
            ->with('projek')
            ->latest('date')
            ->take(5)
            ->get();

        return view('karyawan.index', compact('todayAttendance', 'assignedProjects', 'recentLogbooks'));
    }

    /**
     * Display the Manager Dashboard.
     */
    public function manager(): View
    {
        $stats = [
            'pending_logbooks' => Logbook::where('status', 'submitted')->count(),
            'projek_berjalan' => Projek::where('status', 'aktif')->count(),
            'presensi_hari_ini' => Attendance::whereDate('date', now()->toDateString())->count(),
        ];

        $pendingReviews = Logbook::with(['user', 'projek'])
            ->where('status', 'submitted')
            ->latest()
            ->take(10)
            ->get();

        return view('manager.index', compact('stats', 'pendingReviews'));
    }
}
