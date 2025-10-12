<?php

namespace App\Http\Controllers\Staff\QC;

use App\Http\Controllers\Controller;
use App\Models\QualityControlResult;

class QCLogController extends Controller
{
    /**
     * Show QC history log.
     */
    public function index()
    {
        $logs = QualityControlResult::with(['production', 'qcBy'])
            ->orderByDesc('checked_at')
            ->paginate(30);

        return view('pages.staff.qc.logs', compact('logs'));
    }
}
