<x-layout :title="'Material Usage Report'">
  <x-slot name="header">Material Usage Overview</x-slot>

  <section class="mx-auto w-full max-w-7xl space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-slate-900">Material Usage Summary</h2>
        <p class="text-sm text-slate-600 mt-1">
          Total material consumption from all production batches.
        </p>
      </div>
    </div>

    {{-- Summary / Stats --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-slate-500">Total Materials Tracked</p>
        <p class="mt-1 text-2xl font-bold text-slate-800">{{ $materials->count() ?? 0 }}</p>
      </div>
      <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-slate-500">Most Used Material</p>
        <p class="mt-1 text-lg font-semibold text-indigo-700">
          {{ optional($materials->sortByDesc('total_used')->first()->material)->name ?? '—' }}
        </p>
      </div>
      <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-slate-500">Highest Consumption</p>
        <p class="mt-1 text-lg font-semibold text-green-700">
          {{ number_format($materials->max('total_used') ?? 0, 2) }}
        </p>
      </div>
      <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-slate-500">Average Use per Material</p>
        <p class="mt-1 text-lg font-semibold text-slate-800">
          {{ number_format($materials->avg('total_used') ?? 0, 2) }}
        </p>
      </div>
    </div>

    {{-- Table --}}
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-slate-800">Aggregated Material Usage</h3>
        <span class="text-xs text-slate-500">{{ $materials->count() }} total records</span>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Material Name</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Total Used</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Unit</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Usage Percentage</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @php
              $totalAll = $materials->sum('total_used') ?: 1;
            @endphp

            @forelse ($materials as $m)
              @php
                $percent = round(($m->total_used / $totalAll) * 100, 2);
              @endphp
              <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-4 py-2 font-medium text-slate-800">
                  {{ $m->material->name ?? 'Unknown Material' }}
                </td>
                <td class="px-4 py-2 text-slate-700">{{ number_format($m->total_used, 2) }}</td>
                <td class="px-4 py-2 text-slate-700">{{ $m->unit ?? '—' }}</td>
                <td class="px-4 py-2 text-slate-700">
                  <div class="flex items-center gap-2">
                    <div class="h-2 w-24 rounded-full bg-slate-100 overflow-hidden">
                      <div class="h-2 bg-indigo-500" style="width: {{ $percent }}%;"></div>
                    </div>
                    <span class="text-xs text-slate-600">{{ $percent }}%</span>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-4 py-4 text-center text-slate-500 text-sm">
                  No material usage data available.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>
</x-layout>
