<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Logbook\StoreLogbookRequest;
use App\Models\Logbook;
use App\Models\LogbookHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LogbookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $logbooks = Auth::user()->logbooks()
            ->with(['projek', 'reviewer'])
            ->latest('date')
            ->paginate(10);

        return view('karyawan.logbook.index', compact('logbooks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLogbookRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'submitted';

        $logbook = Logbook::create($validated);

        LogbookHistory::create([
            'logbook_id' => $logbook->id,
            'user_id' => Auth::id(),
            'action' => 'submitted',
            'note' => 'Logbook diajukan oleh karyawan',
        ]);

        return back()->with('success', 'Logbook kegiatan harian berhasil disimpan.');
    }
}
