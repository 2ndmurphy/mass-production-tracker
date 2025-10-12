<x-layout :title="'Inventory Overview'">
  <x-slot name="header">Warehouse - Inventory</x-slot>

  {{-- Flash message --}}
  @if (session('success'))
      <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-3 text-green-700 text-sm">
          {{ session('success') }}
      </div>
  @endif

  <section class="mx-auto w-full max-w-6xl">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-800">Current Material Inventory</h2>
      <div class="flex items-center gap-2">
        <a href="{{ route('warehouse.stock.index') }}"
           class="inline-flex items-center gap-2 rounded-md border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v16m8-8H4"/>
          </svg>
          New Stock In / Out
        </a>
      </div>
    </div>

    {{-- Search input --}}
    <div x-data="{ search: '' }" class="rounded-lg border border-slate-200 bg-white shadow-sm">
      <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
        <div class="relative w-full md:w-1/3">
          <input type="text" x-model="search" placeholder="Search materials..."
                 class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
          <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-4.35-4.35M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/>
          </svg>
        </div>
      </div>

      {{-- Inventory Table --}}
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">#</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Material Name</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Unit</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Total Quantity</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @foreach ($inventory as $index => $item)
              <tr x-show="!search || '{{ strtolower($item->name) }}'.includes(search.toLowerCase())">
                <td class="px-4 py-2 text-slate-500">{{ $index + 1 }}</td>
                <td class="px-4 py-2 font-medium text-slate-800">{{ $item->name }}</td>
                <td class="px-4 py-2 text-slate-600">{{ $item->unit ?? '-' }}</td>
                <td class="px-4 py-2 text-slate-700">
                  {{ number_format($item->total_quantity ?? 0, 2) }}
                </td>
                <td class="px-4 py-2">
                  @php
                      $q = $item->total_quantity ?? 0;
                      $status = $q > 100 ? 'good' : ($q > 0 ? 'low' : 'empty');
                  @endphp
                  @switch($status)
                      @case('good')
                          <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">
                            <span class="h-2 w-2 rounded-full bg-green-500"></span> Sufficient
                          </span>
                          @break
                      @case('low')
                          <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span> Low Stock
                          </span>
                          @break
                      @case('empty')
                          <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span> Empty
                          </span>
                          @break
                  @endswitch
                </td>
              </tr>
            @endforeach
            @if ($inventory->isEmpty())
              <tr>
                <td colspan="5" class="px-4 py-6 text-center text-slate-500 text-sm">
                  No materials found.
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>

      {{-- Summary Footer --}}
      <div class="border-t border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-500">
        Showing {{ $inventory->count() }} materials
      </div>
    </div>
  </section>
</x-layout>
