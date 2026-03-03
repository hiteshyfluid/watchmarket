@extends('layouts.admin')
@section('title', 'All Adverts')

@section('content')
<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or seller..."
        class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 flex-1 min-w-48">
    <select name="status" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
        <option value="">All Statuses</option>
        @foreach(['draft' => 'Draft', 'active' => 'Active', 'paused' => 'Paused', 'sold' => 'Sold', 'expired' => 'Expired'] as $val => $label)
        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded text-sm font-medium hover:bg-gray-700">Filter</button>
    @if(request()->hasAny(['search', 'status']))
    <a href="{{ route('admin.adverts.index') }}" class="px-4 py-2 rounded text-sm text-gray-600 border hover:bg-gray-50">Clear</a>
    @endif
</form>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
            <tr>
                <th class="px-4 py-3 text-left">Image</th>
                <th class="px-4 py-3 text-left">Title</th>
                <th class="px-4 py-3 text-left">Seller</th>
                <th class="px-4 py-3 text-left">Brand / Model</th>
                <th class="px-4 py-3 text-left">Gallery</th>
                <th class="px-4 py-3 text-right">Price</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Featured</th>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($adverts as $advert)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    @if($advert->main_image)
                        <img src="{{ $advert->mainImageUrl() }}" class="w-12 h-10 object-cover rounded" alt="">
                    @else
                        <div class="w-12 h-10 bg-gray-200 rounded"></div>
                    @endif
                </td>
                <td class="px-4 py-3 font-medium text-gray-900 max-w-xs">
                    <span title="{{ $advert->title }}">{{ \Illuminate\Support\Str::limit($advert->title, 45) }}</span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                    {{ $advert->user->name ?? '-' }}<br>
                    <span class="text-gray-400">{{ $advert->user->email ?? '' }}</span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                    {{ $advert->brand->name ?? '-' }}<br>
                    <span class="text-gray-400">{{ $advert->model->name ?? '' }}</span>
                </td>
                <td class="px-4 py-3">
                    @if($advert->images->count())
                        <div class="flex items-center gap-1">
                            @foreach($advert->images->take(3) as $img)
                                <img src="{{ str_starts_with(ltrim((string) $img->image_path, '/'), 'images/') ? asset(ltrim((string) $img->image_path, '/')) : asset('storage/' . ltrim((string) $img->image_path, '/')) }}" alt="" class="w-8 h-8 rounded object-cover border border-gray-200">
                            @endforeach
                            @if($advert->images->count() > 3)
                                <span class="text-[11px] text-gray-500">+{{ $advert->images->count() - 3 }}</span>
                            @endif
                        </div>
                    @else
                        <span class="text-xs text-gray-400">No gallery</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right font-semibold">&pound;{{ number_format((float) $advert->price, 2) }}</td>
                <td class="px-4 py-3 text-center">
                    <form method="POST" action="{{ route('admin.adverts.status', $advert) }}">
                        @csrf @method('PATCH')
                        <select name="status" onchange="this.form.submit()"
                            class="text-xs border-0 rounded px-1 py-0.5 {{ $advert->statusBadgeClass() }} cursor-pointer focus:ring-1 focus:ring-gray-300">
                            @foreach(['draft' => 'Draft', 'active' => 'Active', 'paused' => 'Paused', 'sold' => 'Sold', 'expired' => 'Expired'] as $val => $label)
                            <option value="{{ $val }}" {{ $advert->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </td>
                <td class="px-4 py-3 text-center">
                    <form method="POST" action="{{ route('admin.adverts.featured', $advert) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="is_featured" value="{{ $advert->is_featured ? '0' : '1' }}">
                        <button type="submit" class="text-xs px-2 py-1 rounded {{ $advert->is_featured ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $advert->is_featured ? 'Featured' : 'Set Featured' }}
                        </button>
                    </form>
                </td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $advert->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.adverts.show', $advert) }}" class="text-gray-700 hover:underline text-xs mr-2">View</a>
                    <a href="{{ route('admin.adverts.edit', $advert) }}" class="text-blue-600 hover:underline text-xs mr-2">Edit</a>
                    <form method="POST" action="{{ route('admin.adverts.destroy', $advert) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline text-xs"
                            onclick="return confirm('Delete this advert?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" class="px-6 py-10 text-center text-gray-400">No adverts found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-3 border-t">{{ $adverts->links() }}</div>
</div>
@endsection
