<x-layout :title="'Production Detail'">
		<x-slot name="header">Production Batch Detail</x-slot>

		<section class="mx-auto w-full max-w-6xl">
				{{-- Back link --}}
				<div class="mb-4 flex items-center justify-between">
						<h2 class="text-lg font-semibold text-slate-800">
								Batch: <span class="text-indigo-700">{{ $production->production_code }}</span>
						</h2>
						<a class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
								href="{{ route('production.index') }}">
								← Back to List
						</a>
				</div>

				{{-- Batch Info --}}
				<div class="mb-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
						<div class="grid gap-4 text-sm md:grid-cols-2">
								<div>
										<p class="font-medium text-slate-700">Production Date</p>
										<p class="text-slate-600">{{ $production->production_date?->format('d M Y') ?? '-' }}</p>
								</div>
								<div>
										<p class="font-medium text-slate-700">Shift</p>
										<p class="text-slate-600">{{ $production->shift ?? '-' }}</p>
								</div>
								<div>
										<p class="font-medium text-slate-700">Cartons</p>
										<p class="text-slate-600">{{ $production->quantity_carton ?? '-' }}</p>
								</div>
								<div>
										<p class="font-medium text-slate-700">Status</p>
										@php
												$statusColor = match ($production->status) {
												    'planned' => 'bg-sky-50 text-sky-700',
												    'in_progress' => 'bg-amber-50 text-amber-700',
												    'qc_pending' => 'bg-green-50 text-green-700',
												    default => 'bg-slate-100 text-slate-600',
												};
										@endphp
										<span class="{{ $statusColor }} inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium">
												{{ ucfirst(str_replace('_', ' ', $production->status)) }}
										</span>
								</div>
						</div>

						<div class="mt-4">
								<p class="font-medium text-slate-700">Notes</p>
								<p class="whitespace-pre-line text-slate-600">{{ $production->notes ?? '—' }}</p>
						</div>
				</div>

				{{-- Progress Logging --}}
				<div class="mb-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
						<h3 class="mb-3 text-sm font-semibold text-slate-700">Progress Logging</h3>
						<form action="{{ route('production.update-status', $production->id) }}" class="grid gap-4 md:grid-cols-2"
								method="POST">
								@csrf
								@method('PATCH')

								{{-- Status --}}
								<div>
										<label class="mb-1 block text-sm font-medium text-slate-700">Update Status</label>
										<select
												class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
												name="status" required>
												<option @selected($production->status === 'in_progress') value="in_progress">In Progress</option>
												<option @selected($production->status === 'qc_pending') value="qc_pending">Mark as QC Pending</option>
										</select>
								</div>

								{{-- Notes --}}
								<div class="md:col-span-2">
										<label class="mb-1 block text-sm font-medium text-slate-700">Progress Notes</label>
										<textarea
										  class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
										  name="notes" placeholder="Describe current production progress..." rows="2">{{ old('notes', $production->notes) }}</textarea>
								</div>

								{{-- Submit --}}
								<div class="flex justify-end md:col-span-2">
										<button
												class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
												type="submit">
												Save Progress
										</button>
								</div>
						</form>
				</div>

				{{-- Materials Used --}}
				<div class="mb-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
						<h3 class="mb-3 text-sm font-semibold text-slate-700">Materials Used</h3>

						{{-- Table --}}
						<div class="mb-4 overflow-x-auto">
								<table class="min-w-full divide-y divide-slate-200 text-sm">
										<thead class="bg-slate-50">
												<tr>
														<th class="px-4 py-2 text-left font-semibold text-slate-700">Material</th>
														<th class="px-4 py-2 text-left font-semibold text-slate-700">Quantity Used</th>
														<th class="px-4 py-2 text-left font-semibold text-slate-700">Unit</th>
												</tr>
										</thead>
										<tbody class="divide-y divide-slate-100 bg-white">
												@forelse ($production->productionMaterials as $pm)
														<tr>
																<td class="px-4 py-2 text-slate-800">{{ $pm->material->name ?? '-' }}</td>
																<td class="px-4 py-2 text-slate-700">{{ number_format($pm->quantity_used ?? 0, 2) }}</td>
																<td class="px-4 py-2 text-slate-600">{{ $pm->unit ?? '-' }}</td>
														</tr>
												@empty
														<tr>
																<td class="px-4 py-4 text-center text-sm text-slate-500" colspan="3">
																		No material usage recorded yet.
																</td>
														</tr>
												@endforelse
										</tbody>
								</table>
						</div>

						{{-- Add Materials Form --}}
						<form action="{{ route('production.record-materials', $production->id) }}" class="space-y-4" method="POST"
								x-data="{ rows: 1 }">
								@csrf

								<template :key="i" x-for="i in rows">
										<div class="grid gap-3 md:grid-cols-3">
												<div>
														<label class="mb-1 block text-sm font-medium text-slate-700">Material</label>
														<select
																class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
																name="materials[][material_id]" required>
																<option value="">Select material...</option>
																@foreach ($materials ?? [] as $m)
																		<option value="{{ optional($m)->id }}">{{ optional($m)->name }}</option>
																@endforeach
														</select>
												</div>

												<div>
														<label class="mb-1 block text-sm font-medium text-slate-700">Quantity Used</label>
														<input
																class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
																min="0.01" name="materials[][quantity_used]" placeholder="e.g. 25" required step="0.01"
																type="number">
												</div>

												<div>
														<label class="mb-1 block text-sm font-medium text-slate-700">Unit</label>
														<input
																class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
																name="materials[][unit]" placeholder="e.g. kg" required type="text">
												</div>
										</div>
								</template>

								{{-- Add Row Button --}}
								<div class="flex items-center justify-between pt-2">
										<button @click="rows++"
												class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-3 py-1 text-sm text-slate-700 hover:bg-slate-50"
												type="button">
												+ Add Material
										</button>

										<button
												class="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
												type="submit">
												Save Materials
										</button>
								</div>
						</form>
				</div>
		</section>
</x-layout>
