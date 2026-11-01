<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Models\LogbookHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LogbookReviewController extends Controller
{
    public function index(): View
    {
        $logbooks = Logbook::with(['user', 'projek'])
            ->latest('date')
            ->paginate(15);

        return view('manager.logbook.index', compact('logbooks'));
    }

    public function approve(Request $request, Logbook $logbook): RedirectResponse
    {
        $logbook->update([
            'status' => 'approved',
            'review_notes' => $request->input('review_notes', 'Disetujui oleh manager.'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        LogbookHistory::create([
            'logbook_id' => $logbook->id,
            'user_id' => Auth::id(),
            'action' => 'approved',
            'note' => $request->input('review_notes', 'Disetujui oleh manager'),
        ]);

        return back()->with('success', 'Logbook berhasil disetujui.');
    }

    public function reject(Request $request, Logbook $logbook): RedirectResponse
    {
        $request->validate([
            'review_notes' => ['required', 'string', 'max:500'],
        ]);

        $logbook->update([
            'status' => 'rejected',
            'review_notes' => $request->input('review_notes'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        LogbookHistory::create([
            'logbook_id' => $logbook->id,
            'user_id' => Auth::id(),
            'action' => 'rejected',
            'note' => $request->input('review_notes'),
        ]);

        return back()->with('success', 'Logbook ditolak dengan catatan evaluasi.');
    }
}
