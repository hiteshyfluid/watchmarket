@extends('layouts.admin')
@section('title', 'Report #' . $report->id)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.listing-reports.index') }}" class="text-sm text-blue-600 hover:underline">← Back to Reports</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main Detail --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center gap-3 mb-5">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $report->statusBadgeClass() }}">{{ ucfirst($report->status) }}</span>
                <h2 class="text-lg font-semibold text-gray-800">{{ $report->issueLabel() }}</h2>
            </div>

            @if($report->listing_url)
            <div class="mb-4">
                <p class="text-xs uppercase tracking-wider text-gray-400 mb-1">Listing URL / ID</p>
                <p class="text-sm text-blue-600 break-all">{{ $report->listing_url }}</p>
            </div>
            @endif

            <div class="mb-4">
                <p class="text-xs uppercase tracking-wider text-gray-400 mb-1">Description</p>
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap bg-gray-50 p-4 rounded">{{ $report->description }}</p>
            </div>

            @if($report->serial_number)
            <div class="mb-4">
                <p class="text-xs uppercase tracking-wider text-gray-400 mb-1">Watch Serial Number</p>
                <p class="text-sm text-gray-700 font-mono">{{ $report->serial_number }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">

        {{-- Reporter --}}
        <div class="bg-white rounded-lg shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Reporter</h3>
            <div class="space-y-2 text-sm text-gray-600">
                <div><span class="text-gray-400 text-xs">Name</span><br>{{ $report->reporter_name ?: '—' }}</div>
                <div><span class="text-gray-400 text-xs">Email</span><br>
                    <a href="mailto:{{ $report->reporter_email }}" class="text-blue-600 hover:underline">{{ $report->reporter_email }}</a>
                </div>
                <div><span class="text-gray-400 text-xs">Submitted</span><br>{{ $report->created_at->format('d M Y H:i') }}</div>
            </div>
        </div>

        {{-- Update Status --}}
        <div class="bg-white rounded-lg shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Update Status</h3>
            <form method="POST" action="{{ route('admin.listing-reports.status', $report) }}">
                @csrf @method('PATCH')
                <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <option value="new"      {{ $report->status === 'new'      ? 'selected' : '' }}>New</option>
                    <option value="reviewed" {{ $report->status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                    <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
                <button type="submit" class="w-full bg-gray-900 text-white text-sm px-4 py-2 rounded font-medium hover:bg-gray-700">
                    Update Status
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
