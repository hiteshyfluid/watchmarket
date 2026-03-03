@extends('layouts.admin')
@section('title', 'Advert Details')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.adverts.index') }}" class="text-sm text-gray-500 hover:text-gray-800">Back to Adverts</a>
    <a href="{{ route('admin.adverts.edit', $advert) }}" class="px-4 py-2 rounded text-sm font-medium bg-gray-900 text-white hover:bg-gray-700">Edit Advert</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $advert->title }}</h2>

        <p class="text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Main Image (Featured Card Image)</p>
        @if($advert->mainImageUrl())
            <img src="{{ $advert->mainImageUrl() }}" alt="{{ $advert->title }}" class="w-full h-80 object-cover rounded-lg mb-4">
        @else
            <div class="w-full h-80 rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-sm text-gray-400 mb-4">No main image</div>
        @endif

        <p class="text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Gallery Images</p>
        @if($advert->images->count())
            <div class="grid grid-cols-3 md:grid-cols-5 gap-3 mb-4">
                @foreach($advert->images as $img)
                    <img src="{{ str_starts_with(ltrim((string) $img->image_path, '/'), 'images/') ? asset(ltrim((string) $img->image_path, '/')) : asset('storage/' . ltrim((string) $img->image_path, '/')) }}" alt="" class="w-full h-20 object-cover rounded border border-gray-200">
                @endforeach
            </div>
        @else
            <div class="w-full rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-sm text-gray-400 mb-4 py-6">No gallery images</div>
        @endif

        <div class="prose max-w-none text-sm text-gray-700">
            {!! $advert->description !!}
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 space-y-3 text-sm">
        <div><span class="font-semibold">Status:</span> {{ ucfirst($advert->status) }}</div>
        <div><span class="font-semibold">Featured:</span> {{ $advert->is_featured ? 'Yes' : 'No' }}</div>
        <div><span class="font-semibold">Price:</span> &pound;{{ number_format((float) $advert->price, 2) }}</div>
        <div><span class="font-semibold">Seller:</span> {{ $advert->user->name ?? '-' }}</div>
        <div><span class="font-semibold">Email:</span> {{ $advert->user->email ?? '-' }}</div>
        <div><span class="font-semibold">Phone:</span> {{ $advert->user->phone ?? '-' }}</div>
        <div><span class="font-semibold">City:</span> {{ $advert->user->city ?? '-' }}</div>
        <div><span class="font-semibold">Country:</span> {{ $advert->user->country ?? '-' }}</div>
        <div><span class="font-semibold">Brand:</span> {{ $advert->brand->name ?? '-' }}</div>
        <div><span class="font-semibold">Model:</span> {{ $advert->model->name ?? '-' }}</div>
        <div><span class="font-semibold">Year:</span> {{ $advert->year->name ?? '-' }}</div>
        <div><span class="font-semibold">Condition:</span> {{ $advert->condition->name ?? '-' }}</div>
        <div><span class="font-semibold">Paper:</span> {{ $advert->paper->name ?? '-' }}</div>
        <div><span class="font-semibold">Box:</span> {{ $advert->box->name ?? '-' }}</div>
        <div><span class="font-semibold">Created:</span> {{ $advert->created_at->format('d M Y H:i') }}</div>
        <div><span class="font-semibold">Updated:</span> {{ $advert->updated_at->format('d M Y H:i') }}</div>
    </div>
</div>
@endsection
