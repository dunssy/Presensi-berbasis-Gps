<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Logbook;
use App\Models\Projek;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display Attendance Reports.
     */
    public function attendance(Request $request): View
    {
        $query = Attendance::with(['user', 'attendancePoint.projek'])->latest('date');

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $attendances = $query->paginate(20);
        $users = User::where('role', 'karyawan')->get();

        return view('admin.reports.attendance', compact('attendances', 'users'));
    }

    /**
     * Display Logbook Reports.
     */
    public function logbook(Request $request): View
    {
        $query = Logbook::with(['user', 'projek', 'reviewer'])->latest('date');

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request->filled('projek_id')) {
            $query->where('projek_id', $request->projek_id);
        }

        $logbooks = $query->paginate(20);
        $projects = Projek::all();

        return view('admin.reports.logbook', compact('logbooks', 'projects'));
    }
}
