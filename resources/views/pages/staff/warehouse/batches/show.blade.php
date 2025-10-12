<x-layout :title="'Batch Detail'">
  <x-slot name="header">Warehouse – Batch Detail</x-slot>

  <section class="mx-auto w-full max-w-5xl">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-800">
        Batch: <span class="text-indigo-700">{{ $batch->batch_code }}</span>
      </h2>
      <a href="{{ route('warehouse.batches.index') }}"
         class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
        ← Back to Batches
      </a>
    </div>

    {{-- Batch info --}}
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm mb-6">
      <div class="grid md:grid-cols-2 gap-4 text-sm">
        <div>
          <p class="font-medium text-slate-700">Material</p>
          <p class="text-slate-600">{{ $batch->material->name ?? '-' }}</p>
        </div>
        <div>
          <p class="font-medium text-slate-700">Supplier</p>
          <p class="text-slate-600">{{ $batch->supplier->name ?? '-' }}</p>
        </div>
        <div>
          <p class="font-medium text-slate-700">Received By</p>
          <p class="text-slate-600">{{ $batch->receivedBy->name ?? '-' }}</p>
        </div>
        <div>
          <p class="font-medium text-slate-700">Received Date</p>
          <p class="text-slate-600">{{ $batch->received_date?->format('d M Y, H:i') ?? '-' }}</p>
        </div>
        <div>
          <p class="font-medium text-slate-700">Quantity</p>
          <p class="text-slate-600">{{ number_format($batch->quantity, 2) }} {{ $batch->unit }}</p>
        </div>
        <div>
          <p class="font-medium text-slate-700">Status</p>
          @switch($batch->status)
              @case('received')
                  <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">
                    <span class="h-2 w-2 rounded-full bg-green-500"></span> Received
                  </span>
                  @break
              @case('in_use')
                  <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span> In Use
                  </span>
                  @break
              @case('rejected')
                  <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700">
                    <span class="h-2 w-2 rounded-full bg-red-500"></span> Rejected
                  </span>
                  @break
          @endswitch
        </div>
        <div class="md:col-span-2">
          <p class="font-medium text-slate-700">Notes</p>
          <p class="text-slate-600 whitespace-pre-line">{{ $batch->notes ?? '—' }}</p>
        </div>
      </div>
    </div>

    {{-- Movement history --}}
    <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-200 px-5 py-3">
        <h3 class="text-sm font-semibold text-slate-700">Stock Movements</h3>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Type</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Quantity</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Warehouse</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">User</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Date</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Note</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($batch->stockMovements as $move)
              <tr>
                <td class="px-4 py-2 font-medium">
                  @if ($move->type === 'in')
                      <span class="text-green-700">IN</span>
                  @elseif ($move->type === 'out')
                      <span class="text-amber-700">OUT</span>
                  @else
                      <span class="text-slate-600">{{ strtoupper($move->type) }}</span>
                  @endif
                </td>
                <td class="px-4 py-2 text-slate-700">{{ number_format($move->quantity, 2) }} {{ $move->unit }}</td>
                <td class="px-4 py-2 text-slate-600">{{ $move->warehouse->name ?? '-' }}</td>
                <td class="px-4 py-2 text-slate-600">{{ $move->createdBy->name ?? '-' }}</td>
                <td class="px-4 py-2 text-slate-500">{{ $move->created_at->format('d M Y, H:i') }}</td>
                <td class="px-4 py-2 text-slate-600">{{ $move->note ?? '-' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-4 py-6 text-center text-slate-500 text-sm">
                  No movements recorded for this batch.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>
</x-layout>
