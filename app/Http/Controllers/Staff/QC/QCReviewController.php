<?php

namespace App\Http\Controllers\Staff\QC;

use App\Http\Controllers\Controller;
use App\Models\{Production, QualityControlResult};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class QCReviewController extends Controller
{
    /**
     * Show a batch detail and QC form.
     */
    public function show($id)
    {
        $batch = Production::with(['productionMaterials.material', 'startedBy'])->findOrFail($id);

        return view('pages.staff.qc.review', compact('batch'));
    }

    /**
     * Store QC decision (approve / reject / rework).
     */
    public function store(Request $request, $id)
    {
        $batch = Production::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:pass,fail,rework',
            'sample_count' => 'nullable|integer|min:1',
            'defect_type' => 'nullable|string|max:150',
            'action_taken' => 'nullable|string|max:2000',
        ]);

        // Create QC record
        $qc = QualityControlResult::create([
            'production_id' => $batch->id,
            'qc_by' => Auth::id(),
            'sample_count' => $data['sample_count'] ?? 0,
            'status' => $data['status'],
            'defect_type' => $data['defect_type'] ?? null,
            'action_taken' => $data['action_taken'] ?? null,
            'checked_at' => now(),
        ]);

        // Update production batch status based on QC outcome
        if ($data['status'] === 'pass') {
            $batch->status = 'qc_passed';
        } elseif ($data['status'] === 'fail') {
            $batch->status = 'qc_failed';
        } elseif ($data['status'] === 'rework') {
            $batch->status = 'in_progress';
        }

        $batch->save();

        return redirect()->route('qc.index')->with('success', 'QC result recorded successfully.');
    }
}
