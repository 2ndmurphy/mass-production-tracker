<x-layout :title="'Create Production Batch'">
  <x-slot name="header">Create New Production Batch</x-slot>

  <section class="mx-auto w-full max-w-3xl">
    {{-- Back link --}}
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-800">New Production Batch</h2>
      <a href="{{ route('production.index') }}"
         class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
        ← Back to List
      </a>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('production.store') }}"
          class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm space-y-5">
      @csrf

      {{-- Production Code --}}
      {{-- <div>
        <label for="production_code" class="block text-sm font-medium text-slate-700 mb-1">Production Code</label>
        <input type="text" id="production_code" name="production_code" value="{{ old('production_code') }}"
               placeholder="e.g. PROD-2025-001"
               disabled
               class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('production_code')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div> --}}

      {{-- Production Date --}}
      <div>
        <label for="production_date" class="block text-sm font-medium text-slate-700 mb-1">Production Date</label>
        <input type="date" id="production_date" name="production_date" value="{{ old('production_date') }}"
               required
               class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('production_date')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Shift --}}
      <div>
        <label for="shift" class="block text-sm font-medium text-slate-700 mb-1">Shift</label>
        <select id="shift" name="shift" required
                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
          <option value="">Select shift...</option>
          <option value="A" @selected(old('shift') === 'A')>Shift A</option>
          <option value="B" @selected(old('shift') === 'B')>Shift B</option>
          <option value="C" @selected(old('shift') === 'C')>Shift C</option>
        </select>
        @error('shift')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Quantity Carton --}}
      <div>
        <label for="quantity_carton" class="block text-sm font-medium text-slate-700 mb-1">Carton Quantity</label>
        <input type="number" id="quantity_carton" name="quantity_carton" value="{{ old('quantity_carton') }}"
               required min="1" step="1"
               placeholder="e.g. 250"
               class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('quantity_carton')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Notes --}}
      <div>
        <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Notes (optional)</label>
        <textarea id="notes" name="notes" rows="3"
                  placeholder="Additional remarks or instructions..."
                  class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
        @error('notes')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Submit --}}
      <div class="flex justify-end pt-3">
        <button type="submit"
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          Create Batch
        </button>
      </div>
    </form>
  </section>
</x-layout>
