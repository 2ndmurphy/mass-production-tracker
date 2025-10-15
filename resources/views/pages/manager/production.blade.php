<x-layout :title="'Production Monitoring'">
  <x-slot name="header">Production Monitoring</x-slot>

  <section class="mx-auto w-full max-w-7xl space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-slate-900">Production Overview</h2>
        <p class="text-sm text-slate-600 mt-1">All batches across shifts and statuses.</p>
      </div>
    </div>

    {{-- Filters --}}
    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm" x-data="{ status: '{{ request('status') ?? '' }}' }">
      <form method="GET" class="grid md:grid-cols-4 gap-3">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
          <select name="status" x-model="status" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">All</option>
            <option value="planned">Planned</option>
            <option value="in_progress">In Progress</option>
            <option value="qc_pending">QC Pending</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Shift</label>
          <input type="text" name="shift" value="{{ request('shift') }}" placeholder="e.g. A/B/C"
                 class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Date</label>
          <input type="date" name="date" value="{{ request('date') }}"
                 class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="flex items-end">
          <button type="submit"
                  class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            Filter
          </button>
        </div>
      </form>
    </div>

    {{-- Table --}}
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-slate-800">All Production Batches</h3>
        <span class="text-xs text-slate-500">{{ $productions->count() }} total records</span>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Code</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Date</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Shift</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Cartons</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Status</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Started By</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Notes</th>
              <th class="px-4 py-2 text-right font-semibold text-slate-700">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($productions as $p)
              @php
                $statusColor = match($p->status) {
                  'planned' => 'bg-sky-50 text-sky-700',
                  'in_progress' => 'bg-amber-50 text-amber-700',
                  'qc_pending' => 'bg-indigo-50 text-indigo-700',
                  default => 'bg-slate-100 text-slate-600',
                };
              @endphp
              <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-4 py-2 font-medium text-slate-800">{{ $p->production_code ?? '-' }}</td>
                <td class="px-4 py-2 text-slate-700">{{ $p->production_date?->format('d M Y') ?? '-' }}</td>
                <td class="px-4 py-2 text-slate-700">{{ $p->shift ?? '-' }}</td>
                <td class="px-4 py-2 text-slate-700">{{ $p->quantity_carton ?? '—' }}</td>
                <td class="px-4 py-2">
                  <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium {{ $statusColor }}">
                    {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                  </span>
                </td>
                <td class="px-4 py-2 text-slate-700">{{ $p->startedBy->name ?? '—' }}</td>
                <td class="px-4 py-2 text-slate-600 truncate max-w-xs">{{ $p->notes ?? '—' }}</td>
                <td class="px-4 py-2 text-right">
                  <a href="{{ route('manager.production.show', $p->id) }}"
                     class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">View</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-4 py-4 text-center text-slate-500 text-sm">No production batches found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>
</x-layout>
