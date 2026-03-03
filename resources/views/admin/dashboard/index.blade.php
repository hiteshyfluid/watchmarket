@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    @foreach([
        ['label' => 'Total Users',    'value' => $stats['total_users'],    'color' => 'bg-blue-500'],
        ['label' => 'Sellers',        'value' => $stats['total_sellers'],  'color' => 'bg-purple-500'],
        ['label' => 'Total Adverts',  'value' => $stats['total_adverts'],  'color' => 'bg-gray-700'],
        ['label' => 'Active Adverts', 'value' => $stats['active_adverts'], 'color' => 'bg-green-500'],
        ['label' => 'Sold',           'value' => $stats['sold_adverts'],   'color' => 'bg-yellow-500'],
        ['label' => 'Brands',         'value' => $stats['total_brands'],   'color' => 'bg-indigo-500'],
    ] as $stat)
    <div class="bg-white rounded-lg shadow-sm p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full {{ $stat['color'] }} flex-shrink-0"></div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $stat['label'] }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Recent Adverts --}}
<div class="bg-white rounded-lg shadow-sm">
    <div class="px-6 py-4 border-b flex items-center justify-between">
        <h2 class="font-semibold text-gray-800">Recent Adverts</h2>
        <a href="{{ route('admin.adverts.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Seller</th>
                    <th class="px-6 py-3 text-left">Brand</th>
                    <th class="px-6 py-3 text-right">Price</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentAdverts as $advert)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-medium text-gray-800">{{ Str::limit($advert->title, 40) }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $advert->user->name ?? '—' }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $advert->brand->name ?? '—' }}</td>
                    <td class="px-6 py-3 text-right font-medium">£{{ number_format($advert->price, 2) }}</td>
                    <td class="px-6 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $advert->statusBadgeClass() }}">
                            {{ ucfirst($advert->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-gray-400 text-xs">{{ $advert->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">No adverts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
