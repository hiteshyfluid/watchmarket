@extends('layouts.admin')
@section('title', 'Tags')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Add Tag Form --}}
    <div class="bg-white rounded-lg shadow-sm p-6 h-fit">
        <h2 class="text-base font-bold text-gray-800 mb-4">Add New Tag</h2>
        <form method="POST" action="{{ route('admin.tags.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
                    placeholder="e.g. Limited Edition">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full bg-gray-900 text-white px-4 py-2 rounded text-sm font-medium hover:bg-gray-700">
                Add Tag
            </button>
        </form>
    </div>

    {{-- Tags List --}}
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="font-bold text-gray-800">All Tags <span class="text-gray-400 font-normal text-sm">({{ $tags->total() }})</span></h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Slug</th>
                    <th class="px-6 py-3 text-center">Adverts</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" x-data="{ editing: null, editName: '' }">
                @forelse($tags as $tag)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">
                        <span x-show="editing !== {{ $tag->id }}">{{ $tag->name }}</span>
                        <form method="POST" action="{{ route('admin.tags.update', $tag) }}" x-show="editing === {{ $tag->id }}" class="flex gap-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" :value="editName"
                                class="border border-gray-300 rounded px-2 py-1 text-sm flex-1 focus:outline-none focus:ring-1 focus:ring-gray-900">
                            <button type="submit" class="text-green-600 text-xs font-medium">Save</button>
                            <button type="button" @click="editing = null" class="text-gray-400 text-xs">Cancel</button>
                        </form>
                    </td>
                    <td class="px-6 py-3 text-gray-400 font-mono text-xs">{{ $tag->slug }}</td>
                    <td class="px-6 py-3 text-center text-gray-500">{{ $tag->adverts_count }}</td>
                    <td class="px-6 py-3 text-right">
                        <button type="button"
                            @click="editing = {{ $tag->id }}; editName = '{{ addslashes($tag->name) }}'"
                            class="text-blue-600 hover:underline text-xs mr-3">Edit</button>
                        <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs"
                                onclick="return confirm('Delete tag {{ addslashes($tag->name) }}?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400">No tags yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-3 border-t">{{ $tags->links() }}</div>
    </div>
</div>
@endsection
