@extends('layouts.admin')
@section('title', 'Users')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-gray-800">Users <span class="text-gray-400 font-normal text-base">({{ $users->total() }})</span></h2>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
            <tr>
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Role</th>
                <th class="px-6 py-3 text-center">Verified</th>
                <th class="px-6 py-3 text-left">Joined</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500 flex-shrink-0">
                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                        </div>
                        <span class="font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</span>
                    </div>
                </td>
                <td class="px-6 py-3 text-gray-500">{{ $user->email }}</td>
                <td class="px-6 py-3">
                    @php
                        $roleColors = [
                            'admin'          => 'bg-red-100 text-red-800',
                            'private_seller' => 'bg-blue-100 text-blue-800',
                            'trade_seller'   => 'bg-purple-100 text-purple-800',
                            'customer'       => 'bg-gray-100 text-gray-700',
                        ];
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ ucwords(str_replace('_', ' ', $user->role)) }}
                    </span>
                </td>
                <td class="px-6 py-3 text-center">
                    @if($user->email_verified_at)
                        <span class="text-green-500" title="{{ $user->email_verified_at->format('d M Y') }}">✓</span>
                    @else
                        <span class="text-red-400">✗</span>
                    @endif
                </td>
                <td class="px-6 py-3 text-gray-400 text-xs">{{ $user->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-3 border-t">{{ $users->links() }}</div>
</div>
@endsection
