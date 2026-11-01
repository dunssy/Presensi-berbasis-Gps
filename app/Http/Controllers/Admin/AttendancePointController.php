<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendancePoint;
use App\Models\Projek;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendancePointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $points = AttendancePoint::with('projek')
            ->latest()
            ->paginate(10);

        $projects = Projek::where('status', 'aktif')->get();

        return view('admin.attendance_points.index', compact('points', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'projek_id' => ['nullable', 'exists:projeks,id'],
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:10', 'max:5000'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        AttendancePoint::create($validated);

        return redirect()->route('admin.attendance-points.index')->with('success', 'Titik presensi GPS berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttendancePoint $attendancePoint): RedirectResponse
    {
        $validated = $request->validate([
            'projek_id' => ['nullable', 'exists:projeks,id'],
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:10', 'max:5000'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $attendancePoint->update($validated);

        return redirect()->route('admin.attendance-points.index')->with('success', 'Titik presensi GPS berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendancePoint $attendancePoint): RedirectResponse
    {
        $attendancePoint->delete();

        return redirect()->route('admin.attendance-points.index')->with('success', 'Titik presensi berhasil dihapus.');
    }
}
