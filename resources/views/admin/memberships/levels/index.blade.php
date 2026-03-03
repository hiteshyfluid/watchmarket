@extends('layouts.admin')
@section('title', 'Membership Levels')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Membership Levels</h2>
        <p class="text-sm text-gray-500 mt-1">Create and manage plan pricing, trial, expiration and signup access.</p>
    </div>
    <a href="{{ route('admin.membership-levels.create') }}"
       class="bg-gray-900 text-white px-4 py-2 rounded text-sm font-medium hover:bg-gray-700">
        + Add Level
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
            <tr>
                <th class="px-6 py-3 text-left">ID</th>
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Package For</th>
                <th class="px-6 py-3 text-left">Billing Details</th>
                <th class="px-6 py-3 text-left">Expiration</th>
                <th class="px-6 py-3 text-left">Allow Signups</th>
                <th class="px-6 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($levels as $level)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-500">{{ $level->id }}</td>
                <td class="px-6 py-4">
                    <div class="font-semibold text-gray-800">{{ $level->name }}</div>
                    @if(!$level->is_active)
                    <span class="text-[11px] text-red-500">Inactive</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $level->sellerTypeLabel() }}</td>
                <td class="px-6 py-4 text-gray-600">
                    {{ $level->billingSummary() }}
                    @if($level->privatePriceRangeLabel())
                        <div class="text-xs text-gray-500 mt-1">{{ $level->privatePriceRangeLabel() }}</div>
                    @endif
                    @if($level->tradeAdvertLimitLabel())
                        <div class="text-xs text-gray-500 mt-1">{{ $level->tradeAdvertLimitLabel() }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $level->expirationLabel() }}</td>
                <td class="px-6 py-4">
                    <span class="{{ $level->allow_signups ? 'text-blue-600' : 'text-gray-400' }}">
                        {{ $level->allow_signups ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('admin.membership-levels.edit', $level) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                    <a href="{{ route('admin.membership-members.index', ['level_id' => $level->id]) }}" class="text-blue-600 hover:underline mr-3">Members</a>
                    <a href="{{ route('admin.membership-orders.index', ['level_id' => $level->id]) }}" class="text-blue-600 hover:underline mr-3">Orders</a>
                    <form method="POST" action="{{ route('admin.membership-levels.destroy', $level) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline"
                            onclick="return confirm('Delete this level?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                    No membership levels yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-3 border-t">{{ $levels->links() }}</div>
</div>
@endsection
