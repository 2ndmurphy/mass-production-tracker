<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Production;
use App\Models\QualityControlResult;
use App\Models\ProductionMaterial;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    /**
     * Dashboard overview — key metrics.
     */
    public function dashboard()
    {
        $stats = [
            'planned' => Production::where('status', 'planned')->count(),
            'in_progress' => Production::where('status', 'in_progress')->count(),
            'qc_pending' => Production::where('status', 'qc_pending')->count(),
            'completed' => QualityControlResult::where('status', 'pass')->count(),
            'failed' => QualityControlResult::where('status', 'fail')->count()
        ];

        $recentProductions = Production::latest()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentQc = QualityControlResult::with('production')
            ->latest()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('pages.manager.dashboard', compact('stats', 'recentProductions', 'recentQc'));
    }

    /**
     * Production monitoring table.
     */
    public function production()
    {
        $productions = Production::with(['startedBy'])->latest()->get();

        return view('pages.manager.production', compact('productions'));
    }

    /**
     * QC performance overview.
     */
    public function qc()
    {
        $qcLogs = QualityControlResult::with(['production', 'qcBy'])->latest()->get();

        $qcStats = [
            'pass' => $qcLogs->where('status', 'pass')->count(),
            'fail' => $qcLogs->where('status', 'fail')->count(),
            'rework' => $qcLogs->where('status', 'rework')->count(),
        ];

        return view('pages.manager.qc', compact('qcLogs', 'qcStats'));
    }

    /**
     * Material usage summary.
     */
    public function materials()
    {
        $materials = ProductionMaterial::with('material')
            ->selectRaw('material_id, SUM(quantity_used) as total_used, unit')
            ->groupBy('material_id', 'unit')
            ->get();

        return view('pages.manager.material', compact('materials'));
    }
}
