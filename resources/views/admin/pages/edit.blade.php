@extends('layouts.admin')
@section('title', $page->title)

@section('content')
<div class="max-w-5xl space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">{{ $page->title }}</h2>
        <p class="text-sm text-gray-500 mt-1">Edit the content shown on the public {{ $page->title }} page.</p>
    </div>

    <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="space-y-6" id="page-form">
        @csrf
        @method('PUT')

        <section class="bg-white rounded-lg shadow-sm p-6 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Page Title</label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                @error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                <div id="page-editor" style="min-height: 420px;">{!! old('content', $page->content) !!}</div>
                <textarea name="content" id="page-content-input" class="hidden">{{ old('content', $page->content) }}</textarea>
                @error('content')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </section>

        <div class="pt-2">
            <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-700">
                Save Changes
            </button>
        </div>
    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    const pageQuill = new Quill('#page-editor', { theme: 'snow' });
    document.getElementById('page-form').addEventListener('submit', function () {
        document.getElementById('page-content-input').value = pageQuill.root.innerHTML;
    });
</script>
@endsection
