<?php

namespace App\Http\Controllers\Staff\QC;

use App\Http\Controllers\Controller;
use App\Models\Production;
use Illuminate\Http\Request;

class QCController extends Controller
{
    /**
     * Show all pending QC batches.
     */
    public function index()
    {
        $pendingBatches = Production::with(['startedBy'])
            ->where('status', 'qc_pending')
            ->orderByDesc('production_date')
            ->paginate(20);

        return view('pages.staff.qc.index', compact('pendingBatches'));
    }
}
