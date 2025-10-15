<x-layout :title="'Manager Dashboard'">
    <x-slot name="header">Manager Dashboard</x-slot>

    <section class="mx-auto w-full max-w-7xl space-y-6">
        {{-- Greeting --}}
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">
                Welcome back, {{ auth()->user()->name ?? 'Manager' }}
            </h2>
            <p class="mt-1 text-sm text-slate-600">
                Here's an overview of current production status and quality performance.
            </p>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Planned</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ $stats['planned'] ?? 0 }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">In Progress</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">{{ $stats['in_progress'] ?? 0 }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">QC Pending</p>
                <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $stats['qc_pending'] ?? 0 }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Passed QC</p>
                <p class="mt-1 text-2xl font-bold text-green-600">{{ $stats['completed'] ?? 0 }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Failed QC</p>
                <p class="mt-1 text-2xl font-bold text-red-600">{{ $stats['failed'] ?? 0 }}</p>
            </div>
        </div>

        {{-- Recent Productions --}}
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-800">Recent Production Batches</h3>
                <a href="{{ route('manager.production.index') }}" class="text-sm text-indigo-600 hover:underline">View
                    all</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-slate-700">Code</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-700">Date</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-700">Shift</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-700">Status</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-700">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($recentProductions as $p)
                            @php
                                $statusColor = match ($p->status) {
                                    'planned' => 'bg-sky-50 text-sky-700',
                                    'in_progress' => 'bg-amber-50 text-amber-700',
                                    'qc_pending' => 'bg-indigo-50 text-indigo-700',
                                    default => 'bg-slate-100 text-slate-600',
                                };
                              @endphp
                            <tr>
                                <td class="px-4 py-2 text-slate-800">{{ $p->production_code ?? '-' }}</td>
                                <!-- <td class="px-4 py-2 text-slate-700">{{ $p->production_date?->format('d M Y') ?? '-' }}</td> -->
                                <td class="px-4 py-2 text-slate-700">{{ $p->shift ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium {{ $statusColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-slate-600 truncate max-w-xs">{{ $p->notes ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-slate-500 text-sm">No recent production
                                    records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent QC Results --}}
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-800">Recent QC Activities</h3>
                <a href="{{ route('manager.qc.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-slate-700">Production Code</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-700">Checked By</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-700">Samples</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-700">Status</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-700">Checked At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($recentQc as $qc)
                            @php
                                $color = match ($qc->status) {
                                    'pass' => 'text-green-700 bg-green-50',
                                    'fail' => 'text-red-700 bg-red-50',
                                    'rework' => 'text-amber-700 bg-amber-50',
                                    default => 'text-slate-600 bg-slate-100',
                                };
                              @endphp
                            <tr>
                                <td class="px-4 py-2 text-slate-800">{{ $qc->production->production_code ?? '-' }}</td>
                                <td class="px-4 py-2 text-slate-700">{{ $qc->qcBy->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-slate-700">{{ $qc->sample_count ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium {{ $color }}">
                                        {{ ucfirst($qc->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-slate-600">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-slate-500 text-sm">No recent QC records
                                    available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layout>