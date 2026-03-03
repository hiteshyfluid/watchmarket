@extends('layouts.admin')
@section('title', 'Brands & Models')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Brands &amp; Models</h2>
        <p class="text-sm text-gray-500 mt-1">Parent brands and their sub-brands (models)</p>
    </div>
    <div class="flex items-center gap-3">
        <form method="GET" action="{{ route('admin.brands.index') }}" class="flex items-center gap-2">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="Search brand or model..."
                class="w-64 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
            >
            <button type="submit" class="px-3 py-2 rounded text-sm font-medium border border-gray-300 hover:bg-gray-50">Search</button>
            @if(!empty($search))
                <a href="{{ route('admin.brands.index') }}" class="text-sm text-gray-500 hover:text-gray-800">Clear</a>
            @endif
        </form>

        <a href="{{ route('admin.brands.create') }}"
           class="bg-gray-900 text-white px-4 py-2 rounded text-sm font-medium hover:bg-gray-700">
            + Add Brand / Model
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
            <tr>
                <th class="px-6 py-3 text-left">Brand / Model</th>
                <th class="px-6 py-3 text-left">Slug</th>
                <th class="px-6 py-3 text-left">Parent</th>
                <th class="px-6 py-3 text-center">Models</th>
                <th class="px-6 py-3 text-center">Active</th>
                <th class="px-6 py-3 text-center">Featured</th>
                <th class="px-6 py-3 text-center">Popular</th>
                <th class="px-6 py-3 text-center">Image</th>
                <th class="px-6 py-3 text-center">Order</th>
                <th class="px-6 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($brands as $brand)
            <tr class="hover:bg-gray-50 font-medium">
                <td class="px-6 py-3 text-gray-900">{{ $brand->name }}</td>
                <td class="px-6 py-3 text-gray-400 font-mono text-xs">{{ $brand->slug }}</td>
                <td class="px-6 py-3 text-gray-400">-</td>
                <td class="px-6 py-3 text-center text-gray-500">{{ $brand->children->count() }}</td>
                <td class="px-6 py-3 text-center">
                    @if($brand->is_active)
                        <span class="text-green-500">?</span>
                    @else
                        <span class="text-gray-300">?</span>
                    @endif
                </td>
                <td class="px-6 py-3 text-center">
                    @if($brand->is_featured)
                        <span class="text-green-600 text-xs font-semibold">Yes</span>
                    @else
                        <span class="text-gray-400 text-xs">No</span>
                    @endif
                </td>
                <td class="px-6 py-3 text-center">
                    @if($brand->is_popular)
                        <span class="text-green-600 text-xs font-semibold">Yes</span>
                    @else
                        <span class="text-gray-400 text-xs">No</span>
                    @endif
                </td>
                <td class="px-6 py-3 text-center">
                    @if($brand->imageUrl())
                        <img src="{{ $brand->imageUrl() }}" alt="{{ $brand->name }}" class="w-10 h-10 rounded-full object-cover mx-auto border border-gray-200">
                    @else
                        <span class="text-gray-300 text-xs">-</span>
                    @endif
                </td>
                <td class="px-6 py-3 text-center text-gray-400">{{ $brand->sort_order }}</td>
                <td class="px-6 py-3 text-right">
                    <a href="{{ route('admin.brands.edit', $brand) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                    <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline"
                            onclick="return confirm('Delete {{ addslashes($brand->name) }}?')">Delete</button>
                    </form>
                </td>
            </tr>

            @foreach($brand->children as $child)
            <tr class="hover:bg-gray-50 bg-gray-50/50">
                <td class="px-6 py-2 pl-12 text-gray-600 text-xs">? {{ $child->name }}</td>
                <td class="px-6 py-2 text-gray-400 font-mono text-xs">{{ $child->slug }}</td>
                <td class="px-6 py-2 text-gray-400 text-xs">{{ $brand->name }}</td>
                <td class="px-6 py-2 text-center text-gray-400 text-xs">-</td>
                <td class="px-6 py-2 text-center">
                    @if($child->is_active)
                        <span class="text-green-500 text-xs">?</span>
                    @else
                        <span class="text-gray-300 text-xs">?</span>
                    @endif
                </td>
                <td class="px-6 py-2 text-center text-gray-400 text-xs">-</td>
                <td class="px-6 py-2 text-center text-gray-400 text-xs">-</td>
                <td class="px-6 py-2 text-center text-gray-400 text-xs">-</td>
                <td class="px-6 py-2 text-center text-gray-400 text-xs">{{ $child->sort_order }}</td>
                <td class="px-6 py-2 text-right">
                    <a href="{{ route('admin.brands.edit', $child) }}" class="text-blue-600 hover:underline text-xs mr-3">Edit</a>
                    <form method="POST" action="{{ route('admin.brands.destroy', $child) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline text-xs"
                            onclick="return confirm('Delete {{ addslashes($child->name) }}?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
            @empty
            <tr><td colspan="10" class="px-6 py-10 text-center text-gray-400">No brands yet. <a href="{{ route('admin.brands.create') }}" class="text-blue-600 hover:underline">Add one</a>.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-3 border-t">{{ $brands->links() }}</div>
</div>
@endsection
