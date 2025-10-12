<x-layout :title="'Production Orders'">
  <x-slot name="header">Production Orders</x-slot>

  {{-- Flash message --}}
  @if (session('success'))
      <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-3 text-green-700 text-sm">
          {{ session('success') }}
      </div>
  @endif

  <section class="mx-auto w-full max-w-6xl" x-data="{ filter: 'all', search: '' }">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <h2 class="text-lg font-semibold text-slate-800">Production Batches</h2>
      <a href="{{ route('production.create') }}"
         class="inline-flex items-center gap-2 rounded-md border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 5v14m7-7H5"/>
        </svg>
        New Batch
      </a>
    </div>

    {{-- Filter & Search --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex items-center gap-2 text-sm">
        <span class="text-slate-600 font-medium">Filter:</span>
        <button @click="filter = 'all'"
                :class="filter==='all' ? 'bg-indigo-100 text-indigo-700' : 'bg-white text-slate-700 hover:bg-slate-50'"
                class="rounded-md border border-slate-200 px-3 py-1">All</button>
        <button @click="filter = 'planned'"
                :class="filter==='planned' ? 'bg-sky-100 text-sky-700' : 'bg-white text-slate-700 hover:bg-slate-50'"
                class="rounded-md border border-slate-200 px-3 py-1">Planned</button>
        <button @click="filter = 'in_progress'"
                :class="filter==='in_progress' ? 'bg-amber-100 text-amber-700' : 'bg-white text-slate-700 hover:bg-slate-50'"
                class="rounded-md border border-slate-200 px-3 py-1">In Progress</button>
        <button @click="filter = 'qc_pending'"
                :class="filter==='qc_pending' ? 'bg-green-100 text-green-700' : 'bg-white text-slate-700 hover:bg-slate-50'"
                class="rounded-md border border-slate-200 px-3 py-1">QC Pending</button>
      </div>

      <div class="relative w-full sm:w-1/3">
        <input type="text" x-model="search" placeholder="Search by code or shift..."
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
            <th class="px-4 py-2 text-left font-semibold text-slate-700">Code</th>
            <th class="px-4 py-2 text-left font-semibold text-slate-700">Date</th>
            <th class="px-4 py-2 text-left font-semibold text-slate-700">Shift</th>
            <th class="px-4 py-2 text-left font-semibold text-slate-700">Cartons</th>
            <th class="px-4 py-2 text-left font-semibold text-slate-700">Status</th>
            <th class="px-4 py-2 text-left font-semibold text-slate-700">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          @forelse ($productions as $p)
            <tr
              x-show="(filter === 'all' || filter === '{{ $p->status }}')
                      && (!search || '{{ strtolower($p->production_code) }} {{ strtolower($p->shift ?? '') }}'.includes(search.toLowerCase()))"
            >
              <td class="px-4 py-2 font-medium text-slate-800">{{ $p->production_code }}</td>
              <td class="px-4 py-2 text-slate-700">{{ $p->production_date?->format('d M Y') ?? '-' }}</td>
              <td class="px-4 py-2 text-slate-600">{{ $p->shift ?? '-' }}</td>
              <td class="px-4 py-2 text-slate-600">{{ $p->quantity_carton ?? '-' }}</td>
              <td class="px-4 py-2">
                @if ($p->status === 'planned')
                  <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2 py-1 text-xs font-medium text-sky-700">🕓 Planned</span>
                @elseif ($p->status === 'in_progress')
                  <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">⚙️ In Progress</span>
                @elseif ($p->status === 'qc_pending')
                  <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">✅ QC Pending</span>
                @endif
              </td>
              <td class="px-4 py-2 text-center space-x-2">
                {{-- View Details --}}
                <a href="{{ route('production.show', $p->id) }}"
                   class="rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 hover:bg-slate-50">
                  View
                </a>
                
                {{-- Start --}}
                @if ($p->status === 'planned')
                  <form method="POST" action="{{ route('production.start', $p->id) }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 hover:bg-slate-50">
                      Start
                    </button>
                  </form>
                @endif

                {{-- Complete --}}
                @if ($p->status === 'in_progress')
                  <form method="POST" action="{{ route('production.complete', $p->id) }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="rounded-md border border-green-200 bg-green-50 px-2 py-1 text-xs text-green-700 hover:bg-green-100">
                      Complete
                    </button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-6 text-center text-slate-500 text-sm">
                No production batches found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

      {{-- Footer --}}
      <div class="border-t border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-500">
        Showing {{ $productions->count() }} batches
      </div>
    </div>
  </section>
</x-layout>
