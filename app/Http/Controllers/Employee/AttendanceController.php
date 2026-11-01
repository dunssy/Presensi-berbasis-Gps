<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\AttendanceCheckInRequest;
use App\Models\Attendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Store employee check-in with GPS location.
     */
    public function storeCheckIn(AttendanceCheckInRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $existing = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing && $existing->check_in_time) {
            return back()->with('error', 'Anda sudah melakukan presensi masuk hari ini.');
        }

        $validated = $request->validated();

        $attendance = $existing ?? new Attendance;
        $attendance->user_id = $user->id;
        $attendance->attendance_point_id = $validated['attendance_point_id'] ?? null;
        $attendance->date = $today;
        $attendance->check_in_time = now()->toTimeString();
        $attendance->check_in_latitude = $validated['latitude'];
        $attendance->check_in_longitude = $validated['longitude'];
        $attendance->status = 'hadir';
        $attendance->notes = $validated['notes'] ?? 'Presensi masuk via GPS';
        $attendance->save();

        return back()->with('success', 'Presensi masuk berhasil dicatat pada '.$attendance->check_in_time);
    }

    /**
     * Store employee check-out with GPS location.
     */
    public function storeCheckOut(Request $request): RedirectResponse
    {
        $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (! $attendance || ! $attendance->check_in_time) {
            return back()->with('error', 'Anda belum melakukan presensi masuk hari ini.');
        }

        if ($attendance->check_out_time) {
            return back()->with('error', 'Anda sudah melakukan presensi pulang hari ini.');
        }

        $attendance->check_out_time = now()->toTimeString();
        $attendance->check_out_latitude = $request->input('latitude');
        $attendance->check_out_longitude = $request->input('longitude');
        if ($request->filled('notes')) {
            $attendance->notes = ($attendance->notes ? $attendance->notes.' | ' : '').'Pulang: '.$request->input('notes');
        }
        $attendance->save();

        return back()->with('success', 'Presensi pulang berhasil dicatat pada '.$attendance->check_out_time);
    }
}
