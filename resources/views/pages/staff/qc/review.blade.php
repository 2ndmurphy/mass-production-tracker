<x-layout :title="'QC Review'">
  <x-slot name="header">Quality Control – Review Batch</x-slot>

  <section class="mx-auto w-full max-w-5xl">
    {{-- Back button --}}
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-800">
        Reviewing: <span class="text-indigo-700">{{ $batch->production_code }}</span>
      </h2>
      <a href="{{ route('qc.index') }}"
         class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
        ← Back to Pending
      </a>
    </div>

    {{-- Batch info --}}
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm mb-6">
      <h3 class="text-sm font-semibold text-slate-700 mb-3">Batch Details</h3>
      <div class="grid md:grid-cols-2 gap-4 text-sm">
        <div>
          <p class="font-medium text-slate-700">Production Date</p>
          <p class="text-slate-600">{{ $batch->production_date?->format('d M Y') ?? '-' }}</p>
        </div>
        <div>
          <p class="font-medium text-slate-700">Shift</p>
          <p class="text-slate-600">{{ $batch->shift ?? '-' }}</p>
        </div>
        <div>
          <p class="font-medium text-slate-700">Carton Quantity</p>
          <p class="text-slate-600">{{ $batch->quantity_carton ?? '-' }}</p>
        </div>
        <div>
          <p class="font-medium text-slate-700">Started By</p>
          <p class="text-slate-600">{{ $batch->startedBy->name ?? '-' }}</p>
        </div>
      </div>
      <div class="mt-3">
        <p class="font-medium text-slate-700">Notes</p>
        <p class="text-slate-600 text-sm whitespace-pre-line">{{ $batch->notes ?? '—' }}</p>
      </div>
    </div>

    {{-- Materials used --}}
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm mb-6">
      <h3 class="text-sm font-semibold text-slate-700 mb-3">Materials Used</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Material</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Batch</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Quantity Used</th>
              <th class="px-4 py-2 text-left font-semibold text-slate-700">Unit</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @forelse ($batch->productionMaterials as $pm)
              <tr>
                <td class="px-4 py-2 text-slate-700">{{ $pm->material->name ?? '-' }}</td>
                <td class="px-4 py-2 text-slate-600">{{ $pm->rawBatch->batch_code ?? '-' }}</td>
                <td class="px-4 py-2 text-slate-700">{{ number_format($pm->quantity_used ?? 0, 2) }}</td>
                <td class="px-4 py-2 text-slate-600">{{ $pm->unit ?? '-' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-4 py-4 text-center text-slate-500">No material usage recorded.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- QC Form --}}
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm" x-data="{ decision: 'pass' }">
      <h3 class="text-sm font-semibold text-slate-700 mb-3">QC Decision</h3>

      <form method="POST" action="{{ route('qc.review.store', $batch->id) }}" class="grid gap-4 md:grid-cols-2">
        @csrf

        {{-- Status selection --}}
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-slate-700 mb-1">QC Result</label>
          <div class="flex gap-3">
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="status" value="pass" x-model="decision"
                     class="text-green-600 focus:ring-green-500" checked>
              <span class="text-slate-700 text-sm">Pass</span>
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="status" value="fail" x-model="decision"
                     class="text-red-600 focus:ring-red-500">
              <span class="text-slate-700 text-sm">Fail</span>
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="status" value="rework" x-model="decision"
                     class="text-amber-600 focus:ring-amber-500">
              <span class="text-slate-700 text-sm">Rework</span>
            </label>
          </div>
        </div>

        {{-- Sample count --}}
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Sample Count</label>
          <input type="number" name="sample_count" min="1" placeholder="e.g. 20"
                 class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        {{-- Defect type --}}
        <div x-show="decision !== 'pass'" x-transition>
          <label class="block text-sm font-medium text-slate-700 mb-1">Defect Type</label>
          <input type="text" name="defect_type" placeholder="e.g. color inconsistency"
                 class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        {{-- Action Taken --}}
        <div class="md:col-span-2" x-show="decision !== 'pass'" x-transition>
          <label class="block text-sm font-medium text-slate-700 mb-1">Action Taken</label>
          <textarea name="action_taken" rows="2" placeholder="Describe corrective actions..."
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        </div>

        {{-- Submit --}}
        <div class="md:col-span-2 flex justify-end mt-2">
          <button type="submit"
                  class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            Submit QC Result
          </button>
        </div>
      </form>
    </div>
  </section>
</x-layout>
