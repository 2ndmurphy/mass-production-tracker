<x-layout :title="'QC Overview'">
  <x-slot name="header">Quality Control Overview</x-slot>

  <section class="mx-auto w-full max-w-7xl space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-slate-900">Quality Control Summary</h2>
        <p class="text-sm text-slate-600 mt-1">Review pass/fail/rework rates and recent QC activities.</p>
      </div>
    </div>

    {{-- QC Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm text-center">
        <p class="text-sm text-slate-500">Passed</p>
        <p class="mt-1 text-2xl font-bold text-green-600">{{ $qcStats['pass'] ?? 0 }}</p>
      </div>

      <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm text-center">
        <p class="text-sm text-slate-500">Failed</p>
        <p class="mt-1 text-2xl font-bold text-red-600">{{ $qcStats['fail'] ?? 0 }}</p>
      </div>

      <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm text-center">
        <p class="text-sm text-slate-500">Rework</p>
        <p class="mt-1 text-2xl font-bold text-amber-600">{{ $qcStats['rework'] ?? 0 }}</p>
      </div>
    </div>

    {{-- QC Results Table --}}
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-slate-800">Recent Quality Control Results</h3>
        <span class="text-xs text-slate-500">{{ $qcLogs->count() }} total records</span>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Production Code</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Checked By</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Samples</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Status</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Defect Type</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Action Taken</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Checked At</th>
              <th class="px-4 py-2 text-right font-semibold text-slate-700">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($qcLogs as $qc)
              @php
                $badgeColor = match($qc->status) {
                  'pass' => 'bg-green-50 text-green-700',
                  'fail' => 'bg-red-50 text-red-700',
                  'rework' => 'bg-amber-50 text-amber-700',
                  default => 'bg-slate-100 text-slate-600',
                };
              @endphp
              <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-4 py-2 text-slate-800 font-medium">{{ $qc->production->production_code ?? '-' }}</td>
                <td class="px-4 py-2 text-slate-700">{{ $qc->qcBy->name ?? '—' }}</td>
                <td class="px-4 py-2 text-slate-700">{{ $qc->sample_count ?? 0 }}</td>
                <td class="px-4 py-2">
                  <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium {{ $badgeColor }}">
                    {{ ucfirst($qc->status) }}
                  </span>
                </td>
                <td class="px-4 py-2 text-slate-700">{{ $qc->defect_type ?? '—' }}</td>
                <td class="px-4 py-2 text-slate-700 truncate max-w-xs">{{ $qc->action_taken ?? '—' }}</td>
                <td class="px-4 py-2 text-slate-600">-</td>
                <td class="px-4 py-2 text-right">
                  <a href="{{ route('manager.production.show', $qc->production_id) }}"
                     class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">View Production</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-4 py-4 text-center text-slate-500 text-sm">No QC records available.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>
</x-layout>
