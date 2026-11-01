<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjekRequest;
use App\Models\Projek;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $projects = Projek::withCount(['users', 'attendancePoints'])
            ->latest()
            ->paginate(10);

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjekRequest $request): RedirectResponse
    {
        Projek::create($request->validated());

        return redirect()->route('admin.projects.index')->with('success', 'Projek baru berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Projek $project): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:aktif,selesai,tertunda'],
            'description' => ['nullable', 'string'],
        ]);

        $project->update($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Data projek berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Projek $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Projek berhasil dihapus.');
    }
}
