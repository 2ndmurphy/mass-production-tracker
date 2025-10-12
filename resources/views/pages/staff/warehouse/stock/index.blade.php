<x-layout :title="'Stock Management'">
  <x-slot name="header">Warehouse – Stock In / Out</x-slot>

  {{-- Flash Messages --}}
  @if (session('success'))
      <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-3 text-green-700 text-sm">
          {{ session('success') }}
      </div>
  @endif

  {{-- Tabs --}}
  <div x-data="{ tab: 'in' }" class="mx-auto w-full max-w-6xl">
    <div class="flex gap-2 border-b border-slate-200 mb-4">
      <button
        @click="tab='in'"
        :class="tab==='in' ? 'border-b-2 border-indigo-600 text-indigo-700 font-medium' : 'text-slate-600 hover:text-indigo-600'"
        class="px-4 py-2 text-sm focus:outline-none"
      >
        Stock In
      </button>
      <button
        @click="tab='out'"
        :class="tab==='out' ? 'border-b-2 border-indigo-600 text-indigo-700 font-medium' : 'text-slate-600 hover:text-indigo-600'"
        class="px-4 py-2 text-sm focus:outline-none"
      >
        Stock Out
      </button>
    </div>

    {{-- STOCK IN FORM --}}
    <div x-show="tab==='in'" x-transition class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="text-lg font-semibold text-slate-800 mb-4">Record New Stock (Stock In)</h2>

      <form method="POST" action="{{ route('warehouse.stock.store') }}" class="grid gap-4 md:grid-cols-2">
        @csrf

        {{-- Material --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Material</label>
          <select name="material_id" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select material</option>
            @foreach ($materials as $mat)
              <option value="{{ $mat->id }}">{{ $mat->name }} ({{ $mat->unit }})</option>
            @endforeach
          </select>
          @error('material_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Warehouse --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Warehouse</label>
          <select name="warehouse_id" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select warehouse</option>
            @foreach ($warehouses as $wh)
              <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->type }})</option>
            @endforeach
          </select>
          @error('warehouse_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Batch Code --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Batch Code</label>
          <input type="text" name="batch_code" placeholder="e.g. RM-2025-001"
                 class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
          @error('batch_code') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Quantity --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Quantity</label>
          <input type="number" step="0.01" name="quantity" placeholder="0.00"
                 class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
          @error('quantity') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Unit --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Unit</label>
          <input type="text" name="unit" placeholder="kg / pcs / liter"
                 class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
          @error('unit') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Notes --}}
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
          <textarea name="notes" rows="2" placeholder="Optional notes..."
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        </div>

        {{-- Submit --}}
        <div class="md:col-span-2 flex justify-end mt-2">
          <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            Save Stock In
          </button>
        </div>
      </form>
    </div>

    {{-- STOCK OUT FORM --}}
    <div x-show="tab==='out'" x-transition class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="text-lg font-semibold text-slate-800 mb-4">Record Stock Out (Send to Production)</h2>

      <form method="POST" action="{{ route('warehouse.stock.out') }}" class="grid gap-4 md:grid-cols-2">
        @csrf

        {{-- Batch --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Raw Material Batch</label>
          <select name="raw_batch_id" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select batch</option>
            @foreach (\App\Models\RawMaterialBatches::where('status', 'in_use')->get() as $batch)
              <option value="{{ $batch->id }}">{{ $batch->batch_code }} ({{ $batch->material->name }})</option>
            @endforeach
          </select>
          @error('raw_batch_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Warehouse --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Warehouse</label>
          <select name="warehouse_id" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select warehouse</option>
            @foreach ($warehouses as $wh)
              <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->type }})</option>
            @endforeach
          </select>
          @error('warehouse_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Quantity --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Quantity</label>
          <input type="number" step="0.01" name="quantity" placeholder="0.00"
                 class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
          @error('quantity') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Unit --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Unit</label>
          <input type="text" name="unit" placeholder="kg / pcs / liter"
                 class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
          @error('unit') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Note --}}
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-slate-700 mb-1">Note</label>
          <textarea name="note" rows="2" placeholder="Destination or purpose..."
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        </div>

        {{-- Submit --}}
        <div class="md:col-span-2 flex justify-end mt-2">
          <button type="submit" class="rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
            Save Stock Out
          </button>
        </div>
      </form>
    </div>
  </div>
</x-layout>
