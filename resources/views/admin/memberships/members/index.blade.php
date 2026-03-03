@extends('layouts.admin')
@section('title', 'Membership Members')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Members List</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $members->total() }} items</p>
    </div>
</div>

<form method="GET" class="bg-white rounded-lg shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
    <div>
        <label class="block text-xs text-gray-500 mb-1 uppercase tracking-wider">Level</label>
        <select name="level_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            <option value="">All Levels</option>
            @foreach($levels as $level)
            <option value="{{ $level->id }}" {{ (string) request('level_id') === (string) $level->id ? 'selected' : '' }}>
                {{ $level->name }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs text-gray-500 mb-1 uppercase tracking-wider">Search</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Name, email or level"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded text-sm">Search Members</button>
        <a href="{{ route('admin.membership-members.index') }}" class="px-4 py-2 rounded text-sm border text-gray-600 hover:bg-gray-50">Reset</a>
    </div>
</form>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
            <tr>
                <th class="px-5 py-3 text-left">Username</th>
                <th class="px-5 py-3 text-left">ID</th>
                <th class="px-5 py-3 text-left">First Name</th>
                <th class="px-5 py-3 text-left">Last Name</th>
                <th class="px-5 py-3 text-left">Display Name</th>
                <th class="px-5 py-3 text-left">Email</th>
                <th class="px-5 py-3 text-left">Billing Address</th>
                <th class="px-5 py-3 text-left">Level</th>
                <th class="px-5 py-3 text-left">Level ID</th>
                <th class="px-5 py-3 text-left">Fee</th>
                <th class="px-5 py-3 text-left">Registered</th>
                <th class="px-5 py-3 text-left">Start Date</th>
                <th class="px-5 py-3 text-left">End Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($members as $member)
            @php
                $user = $member->user;
                $username = $user ? strstr($user->email, '@', true) : '[deleted]';
            @endphp
            <tr class="hover:bg-gray-50 align-top">
                <td class="px-5 py-4 text-blue-700 font-medium">{{ $username ?: '-' }}</td>
                <td class="px-5 py-4 text-gray-500">{{ $user?->id ?? '-' }}</td>
                <td class="px-5 py-4">{{ $user?->first_name ?? '-' }}</td>
                <td class="px-5 py-4">{{ $user?->last_name ?? '-' }}</td>
                <td class="px-5 py-4">{{ $user?->name ?? '-' }}</td>
                <td class="px-5 py-4">{{ $user?->email ?? '-' }}</td>
                <td class="px-5 py-4 text-gray-600 whitespace-pre-line">{{ $member->billing_address ?: '-' }}</td>
                <td class="px-5 py-4">{{ $member->level?->name ?? '-' }}</td>
                <td class="px-5 py-4">{{ $member->membership_level_id }}</td>
                <td class="px-5 py-4">{{ $member->fee_label ?: '--' }}</td>
                <td class="px-5 py-4">{{ $user?->created_at?->format('F j, Y') ?? '-' }}</td>
                <td class="px-5 py-4">{{ $member->start_date?->format('F j, Y') ?? '-' }}</td>
                <td class="px-5 py-4">{{ $member->end_date?->format('F j, Y') ?? 'Never' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="13" class="px-6 py-12 text-center text-gray-400">No members found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-3 border-t">{{ $members->links() }}</div>
</div>
@endsection

