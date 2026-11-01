<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Projek;
use Illuminate\View\View;

class ProjectMonitoringController extends Controller
{
    /**
     * Display a listing of projects for monitoring.
     */
    public function index(): View
    {
        $projects = Projek::with(['users', 'attendancePoints'])
            ->withCount(['logbooks', 'assignments'])
            ->latest()
            ->paginate(10);

        return view('manager.projects.index', compact('projects'));
    }

    /**
     * Display project details and progress.
     */
    public function show(Projek $project): View
    {
        $project->load(['users', 'attendancePoints', 'logbooks.user']);

        return view('manager.projects.show', compact('project'));
    }
}
