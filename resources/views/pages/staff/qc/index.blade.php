<x-layout :title="'QC Pending Batches'">
  <x-slot name="header">Quality Control – Pending Review</x-slot>

  {{-- Flash message --}}
  @if (session('success'))
      <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-3 text-green-700 text-sm">
          {{ session('success') }}
      </div>
  @endif

  <section class="mx-auto w-full max-w-6xl" x-data="{ search: '' }">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-800">Pending QC Review</h2>
      <a href="{{ route('qc.logs') }}"
         class="inline-flex items-center gap-2 rounded-md border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 17a4 4 0 0 0 4 4h10a4 4 0 0 0 4-4V8a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v9Z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 9h10M7 13h10m-9 4h9"/>
        </svg>
        QC Logs
      </a>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
      {{-- Search --}}
      <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
        <div class="relative w-full md:w-1/3">
          <input type="text" x-model="search" placeholder="Search by production code or shift..."
                 class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
          <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-4.35-4.35M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/>
          </svg>
        </div>
      </div>

      {{-- Table --}}
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Code</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Production Date</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Shift</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Cartons</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Started By</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Status</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($pendingBatches as $batch)
              <tr x-show="!search || '{{ strtolower($batch->production_code) }} {{ strtolower($batch->shift ?? '') }}'.includes(search.toLowerCase())">
                <td class="px-4 py-2 font-medium text-slate-800">{{ $batch->production_code }}</td>
                <td class="px-4 py-2 text-slate-700">{{ $batch->production_date?->format('d M Y') ?? '-' }}</td>
                <td class="px-4 py-2 text-slate-600">{{ $batch->shift ?? '-' }}</td>
                <td class="px-4 py-2 text-slate-700">{{ $batch->quantity_carton ?? '-' }}</td>
                <td class="px-4 py-2 text-slate-600">{{ $batch->startedBy->name ?? '-' }}</td>
                <td class="px-4 py-2 text-center">
                  <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span> QC Pending
                  </span>
                </td>
                <td class="px-4 py-2 text-center">
                  <a href="{{ route('qc.review.show', $batch->id) }}"
                     class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50">
                    Review
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-6 text-center text-slate-500 text-sm">
                  No pending batches for QC review.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Footer --}}
      <div class="border-t border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-500">
        Showing {{ $pendingBatches->count() }} pending batches
      </div>
    </div>
  </section>
</x-layout>
