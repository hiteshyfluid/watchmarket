@extends('layouts.admin')
@section('title', 'Edit Advert')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.adverts.index') }}" class="text-sm text-gray-500 hover:text-gray-800">Back to Adverts</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Edit Advert #{{ $advert->id }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Main Image (Featured Card Image)</p>
                @if($advert->mainImageUrl())
                    <img src="{{ $advert->mainImageUrl() }}" alt="{{ $advert->title }}" class="w-full h-48 object-cover rounded-lg border border-gray-200">
                @else
                    <div class="w-full h-48 rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-sm text-gray-400">No main image</div>
                @endif
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Gallery Images</p>
                @if($advert->images->count())
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($advert->images as $img)
                            <img src="{{ str_starts_with(ltrim((string) $img->image_path, '/'), 'images/') ? asset(ltrim((string) $img->image_path, '/')) : asset('storage/' . ltrim((string) $img->image_path, '/')) }}" alt="" class="w-full h-20 object-cover rounded border border-gray-200">
                        @endforeach
                    </div>
                @else
                    <div class="w-full h-48 rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-sm text-gray-400">No gallery images</div>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('admin.adverts.update', $advert) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Title *</label>
                <input type="text" name="title" value="{{ old('title', $advert->title) }}" required
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Description *</label>
                <textarea name="description" rows="8" required
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">{{ old('description', $advert->description) }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Price *</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $advert->price) }}" required
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Status *</label>
                    <select name="status" required
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                        @foreach(['draft' => 'Draft', 'active' => 'Active', 'paused' => 'Paused', 'sold' => 'Sold', 'expired' => 'Expired'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $advert->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_featured" name="is_featured" value="1"
                    class="rounded border-gray-300" {{ old('is_featured', $advert->is_featured) ? 'checked' : '' }}>
                <label for="is_featured" class="text-sm text-gray-700">Featured Collection</label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <div><span class="font-semibold">Brand:</span> {{ $advert->brand->name ?? '-' }}</div>
                <div><span class="font-semibold">Model:</span> {{ $advert->model->name ?? '-' }}</div>
                <div><span class="font-semibold">Seller:</span> {{ $advert->user->name ?? '-' }}</div>
                <div><span class="font-semibold">Email:</span> {{ $advert->user->email ?? '-' }}</div>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-700">Update</button>
                <a href="{{ route('admin.adverts.show', $advert) }}" class="px-5 py-2 rounded text-sm text-gray-600 border hover:bg-gray-50">View</a>
            </div>
        </form>
    </div>
</div>
@endsection
