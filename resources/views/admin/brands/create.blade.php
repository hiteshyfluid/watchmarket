@extends('layouts.admin')
@section('title', 'Add Brand / Model')

@section('content')
<div class="max-w-xl">
    <div class="mb-6">
        <a href="{{ route('admin.brands.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Back to Brands</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Add Brand or Model</h2>

        <form method="POST" action="{{ route('admin.brands.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
                    placeholder="e.g. Rolex  or  Explorer I">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Slug <span class="text-gray-400 font-normal">(auto-generated)</span></label>
                <input type="text" name="slug" value="{{ old('slug') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-900"
                    placeholder="auto-generated-from-name">
                @error('slug')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Parent Brand <span class="text-gray-400 font-normal">(leave blank if this IS a brand)</span></label>
                <select name="parent_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    <option value="">— None (Top-level Brand) —</option>
                    @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                    @endforeach
                </select>
                @error('parent_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                </div>
                <div class="flex items-center gap-2 mt-6">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                        class="rounded border-gray-300" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm text-gray-700">Active</label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Brand Image</label>
                    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-gray-900">
                    <p class="text-xs text-gray-500 mt-1">Used in homepage featured brands section.</p>
                    @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-center gap-2 mt-6">
                    <input type="checkbox" name="is_featured" value="1" id="is_featured"
                        class="rounded border-gray-300" {{ old('is_featured') ? 'checked' : '' }}>
                    <label for="is_featured" class="text-sm text-gray-700">Featured on Homepage</label>
                </div>
                <div class="flex items-center gap-2 mt-6">
                    <input type="checkbox" name="is_popular" value="1" id="is_popular"
                        class="rounded border-gray-300" {{ old('is_popular') ? 'checked' : '' }}>
                    <label for="is_popular" class="text-sm text-gray-700">Popular Brand</label>
                </div>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-700">Save</button>
                <a href="{{ route('admin.brands.index') }}" class="px-5 py-2 rounded text-sm text-gray-600 border hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
