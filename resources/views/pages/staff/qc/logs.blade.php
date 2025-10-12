<x-layout :title="'QC Logs'">
  <x-slot name="header">Quality Control – Logs History</x-slot>

  <section class="mx-auto w-full max-w-6xl" x-data="{ filter: 'all', search: '' }">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <h2 class="text-lg font-semibold text-slate-800">QC Log History</h2>
      <a href="{{ route('qc.index') }}"
         class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
        ← Back to Pending List
      </a>
    </div>

    {{-- Filter & Search --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex items-center gap-2 text-sm">
        <span class="text-slate-600 font-medium">Filter:</span>
        <button type="button" @click="filter = 'all'"
                :class="filter==='all' ? 'bg-indigo-100 text-indigo-700' : 'bg-white text-slate-700 hover:bg-slate-50'"
                class="rounded-md border border-slate-200 px-3 py-1">All</button>
        <button type="button" @click="filter = 'pass'"
                :class="filter==='pass' ? 'bg-green-100 text-green-700' : 'bg-white text-slate-700 hover:bg-slate-50'"
                class="rounded-md border border-slate-200 px-3 py-1">Pass</button>
        <button type="button" @click="filter = 'fail'"
                :class="filter==='fail' ? 'bg-red-100 text-red-700' : 'bg-white text-slate-700 hover:bg-slate-50'"
                class="rounded-md border border-slate-200 px-3 py-1">Fail</button>
        <button type="button" @click="filter = 'rework'"
                :class="filter==='rework' ? 'bg-amber-100 text-amber-700' : 'bg-white text-slate-700 hover:bg-slate-50'"
                class="rounded-md border border-slate-200 px-3 py-1">Rework</button>
      </div>

      <div class="relative w-full sm:w-1/3">
        <input type="text" x-model="search" placeholder="Search by batch code..."
               class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
        <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-4.35-4.35M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/>
        </svg>
      </div>
    </div>

    {{-- Table --}}
    <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-x-auto">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-2 text-left font-semibold text-slate-700">Batch Code</th>
            <th class="px-4 py-2 text-left font-semibold text-slate-700">Date</th>
            <th class="px-4 py-2 text-left font-semibold text-slate-700">QC Result</th>
            <th class="px-4 py-2 text-left font-semibold text-slate-700">QC By</th>
            <th class="px-4 py-2 text-left font-semibold text-slate-700">Defect Type</th>
            <th class="px-4 py-2 text-left font-semibold text-slate-700">Action Taken</th>
            <th class="px-4 py-2 text-left font-semibold text-slate-700">Production Staff</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          @forelse ($logs as $log)
            <tr
              x-show="(filter === 'all' || filter === '{{ $log->status }}')
                      && (!search || '{{ strtolower($log->production->production_code) }}'.includes(search.toLowerCase()))"
            >
              <td class="px-4 py-2 font-medium text-slate-800">{{ $log->production->production_code ?? '-' }}</td>
              <td class="px-4 py-2 text-slate-700">{{ $log->created_at?->format('d M Y, H:i') ?? '-' }}</td>
              <td class="px-4 py-2">
                @if ($log->status === 'pass')
                  <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">
                    ✅ Passed
                  </span>
                @elseif ($log->status === 'fail')
                  <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700">
                    ❌ Failed
                  </span>
                @elseif ($log->status === 'rework')
                  <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">
                    🔁 Rework
                  </span>
                @endif
              </td>
              <td class="px-4 py-2 text-slate-700">{{ $log->qcBy->name ?? '-' }}</td>
              <td class="px-4 py-2 text-slate-600">{{ $log->defect_type ?? '-' }}</td>
              <td class="px-4 py-2 text-slate-600">{{ Str::limit($log->action_taken ?? '-', 40) }}</td>
              <td class="px-4 py-2 text-slate-600">{{ $log->batch->startedBy->name ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-4 py-6 text-center text-slate-500 text-sm">
                No QC log records found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

      {{-- Footer --}}
      <div class="border-t border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-500">
        Showing {{ $logs->count() }} QC entries
      </div>
    </div>
  </section>
</x-layout>
