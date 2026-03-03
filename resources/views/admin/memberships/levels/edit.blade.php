@extends('layouts.admin')
@section('title', 'Edit Membership Level')

@section('content')
<div class="max-w-5xl">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.membership-levels.index') }}" class="text-sm text-gray-500 hover:text-gray-800">&larr; Back to Membership Levels</a>
            <a href="{{ route('admin.membership-members.index', ['level_id' => $level->id]) }}" class="px-3 py-1.5 border border-blue-500 text-blue-600 rounded text-sm hover:bg-blue-50">View Members</a>
            <a href="{{ route('admin.membership-orders.index', ['level_id' => $level->id]) }}" class="px-3 py-1.5 border border-blue-500 text-blue-600 rounded text-sm hover:bg-blue-50">View Orders</a>
        </div>
        <div class="text-xs text-gray-400">Level ID: {{ $level->id }}</div>
    </div>

    <form method="POST" action="{{ route('admin.membership-levels.update', $level) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.memberships.levels._form')
    </form>
</div>
@endsection
