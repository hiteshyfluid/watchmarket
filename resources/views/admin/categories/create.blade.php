@extends('layouts.admin')
@section('title', 'Add Category')

@section('content')
<div class="max-w-xl">
    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Back to Categories</a>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Add Category</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Slug</label>
                <input type="text" name="slug" value="{{ old('slug') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-900"
                    placeholder="auto-generated">
                @error('slug')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Parent Category</label>
                <select name="parent_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    <option value="">— None —</option>
                    @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Description</label>
                <textarea name="description" rows="3"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div class="flex items-center gap-2 mt-6">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                        class="rounded border-gray-300" checked>
                    <label for="is_active" class="text-sm text-gray-700">Active</label>
                </div>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-700">Save</button>
                <a href="{{ route('admin.categories.index') }}" class="px-5 py-2 rounded text-sm text-gray-600 border hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
