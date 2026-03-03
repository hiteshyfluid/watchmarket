@extends('layouts.admin')
@section('title', 'SEO Meta')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">SEO Meta Tags</h2>
    <p class="text-sm text-gray-500 mt-1">Set page-wise meta title and meta description for all named GET pages.</p>
</div>

<form method="POST" action="{{ route('admin.seo.update') }}">
    @csrf

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Route</th>
                        <th class="px-4 py-3 text-left">URL</th>
                        <th class="px-4 py-3 text-left">Meta Title</th>
                        <th class="px-4 py-3 text-left">Meta Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($routes as $route)
                        @php $row = $existing[$route['name']] ?? null; @endphp
                        <tr class="align-top">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $route['name'] }}</td>
                            <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $route['uri'] }}</td>
                            <td class="px-4 py-3">
                                <input
                                    type="text"
                                    name="entries[{{ $route['name'] }}][meta_title]"
                                    value="{{ old('entries.'.$route['name'].'.meta_title', $row->meta_title ?? '') }}"
                                    placeholder="Enter meta title"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
                                >
                            </td>
                            <td class="px-4 py-3">
                                <textarea
                                    name="entries[{{ $route['name'] }}][meta_description]"
                                    rows="2"
                                    placeholder="Enter meta description"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
                                >{{ old('entries.'.$route['name'].'.meta_description', $row->meta_description ?? '') }}</textarea>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400">No routes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-700">
            Save SEO Meta
        </button>
    </div>
</form>
@endsection

