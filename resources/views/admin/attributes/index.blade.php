@extends('layouts.admin')
@section('title', $typeLabel)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Add Option Form --}}
    <div class="bg-white rounded-lg shadow-sm p-6 h-fit">
        <h2 class="text-base font-bold text-gray-800 mb-1">Add Option</h2>
        <p class="text-xs text-gray-500 mb-4">{{ $typeLabel }}</p>
        <form method="POST" action="{{ route('admin.attributes.store', $type) }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
                    placeholder="e.g. Automatic">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            </div>

            <button type="submit" class="w-full bg-gray-900 text-white px-4 py-2 rounded text-sm font-medium hover:bg-gray-700">
                Add Option
            </button>
        </form>
    </div>

    {{-- Options List --}}
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="font-bold text-gray-800">
                {{ $typeLabel }} Options
                <span class="text-gray-400 font-normal text-sm">({{ $options->count() }})</span>
            </h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-center">Order</th>
                    <th class="px-6 py-3 text-center">Active</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" x-data="{ editing: null, editName: '', editOrder: 0, editActive: true }">
                @forelse($options as $option)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">
                        <span x-show="editing !== {{ $option->id }}">{{ $option->name }}</span>
                        <div x-show="editing === {{ $option->id }}">
                            <form method="POST" action="{{ route('admin.attributes.update', [$type, $option]) }}" class="flex gap-2 items-center">
                                @csrf @method('PUT')
                                <input type="text" name="name" :value="editName"
                                    class="border border-gray-300 rounded px-2 py-1 text-sm w-36 focus:outline-none focus:ring-1 focus:ring-gray-900">
                                <input type="number" name="sort_order" :value="editOrder" min="0"
                                    class="border border-gray-300 rounded px-2 py-1 text-sm w-16 focus:outline-none focus:ring-1 focus:ring-gray-900">
                                <label class="flex items-center gap-1 text-xs text-gray-600">
                                    <input type="checkbox" name="is_active" value="1" x-model="editActive" class="rounded border-gray-300">
                                    Active
                                </label>
                                <button type="submit" class="text-green-600 text-xs font-medium">Save</button>
                                <button type="button" @click="editing = null" class="text-gray-400 text-xs">✕</button>
                            </form>
                        </div>
                    </td>
                    <td class="px-6 py-3 text-center text-gray-400" x-show="editing !== {{ $option->id }}">{{ $option->sort_order }}</td>
                    <td class="px-6 py-3 text-center" x-show="editing !== {{ $option->id }}">
                        <span class="{{ $option->is_active ? 'text-green-500' : 'text-gray-300' }}">●</span>
                    </td>
                    <td class="px-6 py-3 text-right" x-show="editing !== {{ $option->id }}">
                        <button type="button"
                            @click="editing = {{ $option->id }}; editName = '{{ addslashes($option->name) }}'; editOrder = {{ $option->sort_order }}; editActive = {{ $option->is_active ? 'true' : 'false' }}"
                            class="text-blue-600 hover:underline text-xs mr-3">Edit</button>
                        <form method="POST" action="{{ route('admin.attributes.destroy', [$type, $option]) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs"
                                onclick="return confirm('Delete this option?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400">No options yet. Add one using the form.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
