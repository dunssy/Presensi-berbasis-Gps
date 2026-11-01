<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectAssignment;
use App\Models\Projek;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $assignments = ProjectAssignment::with(['projek', 'user'])
            ->latest()
            ->paginate(15);

        $projects = Projek::where('status', 'aktif')->get();
        $users = User::where('is_active', true)->get();

        return view('admin.assignments.index', compact('assignments', 'projects', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'projek_id' => ['required', 'exists:projeks,id'],
            'user_id' => ['required', 'exists:users,id'],
            'role_in_project' => ['required', 'string', 'max:50'],
            'assigned_at' => ['required', 'date'],
        ]);

        ProjectAssignment::updateOrCreate(
            ['projek_id' => $validated['projek_id'], 'user_id' => $validated['user_id']],
            [
                'role_in_project' => $validated['role_in_project'],
                'assigned_at' => $validated['assigned_at'],
            ]
        );

        return redirect()->route('admin.assignments.index')->with('success', 'Penugasan karyawan ke proyek berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectAssignment $assignment): RedirectResponse
    {
        $assignment->delete();

        return redirect()->route('admin.assignments.index')->with('success', 'Penugasan berhasil dihapus.');
    }
}
