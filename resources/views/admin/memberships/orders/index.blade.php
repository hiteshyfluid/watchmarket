@extends('layouts.admin')
@section('title', 'Membership Orders')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Orders</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $orders->total() }} orders found.</p>
    </div>
</div>

<form method="GET" class="bg-white rounded-lg shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-5 gap-3">
    <div>
        <label class="block text-xs text-gray-500 mb-1 uppercase tracking-wider">Level</label>
        <select name="level_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            <option value="">All</option>
            @foreach($levels as $level)
            <option value="{{ $level->id }}" {{ (string) request('level_id') === (string) $level->id ? 'selected' : '' }}>
                {{ $level->name }}
            </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1 uppercase tracking-wider">Status</label>
        <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            <option value="">All</option>
            @foreach($statuses as $status)
            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                {{ ucfirst($status) }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs text-gray-500 mb-1 uppercase tracking-wider">Search</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Code, transaction, user"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded text-sm">Search Orders</button>
        <a href="{{ route('admin.membership-orders.index') }}" class="px-4 py-2 rounded text-sm border text-gray-600 hover:bg-gray-50">Reset</a>
    </div>
</form>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
            <tr>
                <th class="px-5 py-3 text-left">Code</th>
                <th class="px-5 py-3 text-left">User</th>
                <th class="px-5 py-3 text-left">Level</th>
                <th class="px-5 py-3 text-left">Total</th>
                <th class="px-5 py-3 text-left">Billing</th>
                <th class="px-5 py-3 text-left">Gateway</th>
                <th class="px-5 py-3 text-left">Transaction IDs</th>
                <th class="px-5 py-3 text-left">Status</th>
                <th class="px-5 py-3 text-left">Date</th>
                <th class="px-5 py-3 text-left">Invoice</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50 align-top">
                <td class="px-5 py-4 font-semibold text-blue-700">{{ $order->code }}</td>
                <td class="px-5 py-4">
                    @if($order->user)
                    <div class="font-medium">{{ $order->user->name }}</div>
                    <div class="text-gray-600">{{ $order->user->email }}</div>
                    @else
                    <span class="text-gray-400">[deleted]</span>
                    @endif
                </td>
                <td class="px-5 py-4">{{ $order->level?->name ?? '-' }}</td>
                <td class="px-5 py-4">£{{ number_format((float) $order->total, 2) }}</td>
                <td class="px-5 py-4 text-gray-600 whitespace-pre-line">{{ $order->billing_details ?: '--' }}</td>
                <td class="px-5 py-4">{{ $order->gateway ?: '--' }}</td>
                <td class="px-5 py-4">
                    <div>Payment: {{ $order->payment_transaction_id ?: 'N/A' }}</div>
                    <div>Subscription: {{ $order->subscription_transaction_id ?: 'N/A' }}</div>
                </td>
                <td class="px-5 py-4">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        {{ $order->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td class="px-5 py-4">{{ $order->ordered_at?->format('F j, Y \a\t g:i a') ?? '-' }}</td>
                <td class="px-5 py-4">
                    <a href="{{ route('admin.membership-orders.invoice', $order) }}" class="text-blue-600 hover:underline">Download PDF</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="px-6 py-12 text-center text-gray-400">No orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-3 border-t">{{ $orders->links() }}</div>
</div>
@endsection
