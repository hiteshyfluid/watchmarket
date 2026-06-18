@extends('layouts.admin')
@section('title', 'Listing Reports')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">Listing Reports</h2>
        <p class="text-sm text-gray-500">Reports submitted by the public about suspicious listings</p>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="flex gap-3 mb-6">
    <select name="status" onchange="this.form.submit()"
            class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
        <option value="">All Statuses</option>
        <option value="new"      {{ request('status') === 'new'      ? 'selected' : '' }}>New</option>
        <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
    </select>
</form>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
            <tr>
                <th class="px-5 py-3 text-left">#</th>
                <th class="px-5 py-3 text-left">Issue Type</th>
                <th class="px-5 py-3 text-left">Reporter</th>
                <th class="px-5 py-3 text-left">Listing URL</th>
                <th class="px-5 py-3 text-left">Status</th>
                <th class="px-5 py-3 text-left">Date</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($reports as $report)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 text-gray-400 text-xs">{{ $report->id }}</td>
                <td class="px-5 py-3 font-medium text-gray-800">{{ $report->issueLabel() }}</td>
                <td class="px-5 py-3 text-gray-500">
                    <div>{{ $report->reporter_name ?: '—' }}</div>
                    <div class="text-xs text-gray-400">{{ $report->reporter_email }}</div>
                </td>
                <td class="px-5 py-3 text-gray-500 max-w-[180px] truncate text-xs">
                    {{ $report->listing_url ?: '—' }}
                </td>
                <td class="px-5 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $report->statusBadgeClass() }}">
                        {{ ucfirst($report->status) }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-400 text-xs">{{ $report->created_at->format('d M Y H:i') }}</td>
                <td class="px-5 py-3">
                    <a href="{{ route('admin.listing-reports.show', $report) }}"
                       class="text-blue-600 hover:underline text-xs font-medium">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-12 text-center text-gray-400">No reports yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($reports->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $reports->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
