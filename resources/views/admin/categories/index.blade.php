@extends('layouts.admin')
@section('title', 'Categories')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-gray-800">Categories</h2>
    <a href="{{ route('admin.categories.create') }}"
       class="bg-gray-900 text-white px-4 py-2 rounded text-sm font-medium hover:bg-gray-700">
        + Add Category
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
            <tr>
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Slug</th>
                <th class="px-6 py-3 text-left">Parent</th>
                <th class="px-6 py-3 text-center">Sub-cats</th>
                <th class="px-6 py-3 text-center">Active</th>
                <th class="px-6 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($categories as $cat)
            <tr class="hover:bg-gray-50 font-medium">
                <td class="px-6 py-3 text-gray-900">{{ $cat->name }}</td>
                <td class="px-6 py-3 text-gray-400 font-mono text-xs">{{ $cat->slug }}</td>
                <td class="px-6 py-3 text-gray-400">—</td>
                <td class="px-6 py-3 text-center text-gray-500">{{ $cat->children->count() }}</td>
                <td class="px-6 py-3 text-center">
                    <span class="{{ $cat->is_active ? 'text-green-500' : 'text-gray-300' }}">●</span>
                </td>
                <td class="px-6 py-3 text-right">
                    <a href="{{ route('admin.categories.edit', $cat) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline"
                            onclick="return confirm('Delete {{ addslashes($cat->name) }}?')">Delete</button>
                    </form>
                </td>
            </tr>
            @foreach($cat->children as $child)
            <tr class="hover:bg-gray-50 bg-gray-50/50">
                <td class="px-6 py-2 pl-12 text-gray-600 text-xs">↳ {{ $child->name }}</td>
                <td class="px-6 py-2 text-gray-400 font-mono text-xs">{{ $child->slug }}</td>
                <td class="px-6 py-2 text-gray-400 text-xs">{{ $cat->name }}</td>
                <td class="px-6 py-2 text-center text-gray-400 text-xs">—</td>
                <td class="px-6 py-2 text-center">
                    <span class="{{ $child->is_active ? 'text-green-500' : 'text-gray-300' }} text-xs">●</span>
                </td>
                <td class="px-6 py-2 text-right">
                    <a href="{{ route('admin.categories.edit', $child) }}" class="text-blue-600 hover:underline text-xs mr-3">Edit</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $child) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline text-xs"
                            onclick="return confirm('Delete {{ addslashes($child->name) }}?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
            @empty
            <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">No categories yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-3 border-t">{{ $categories->links() }}</div>
</div>
@endsection
