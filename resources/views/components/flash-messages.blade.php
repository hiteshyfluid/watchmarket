@props(['class' => ''])

@if (session('success') || session('error'))
    <div {{ $attributes->merge(['class' => $class]) }}>
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 {{ session('success') ? 'mt-3' : '' }}">
                {{ session('error') }}
            </div>
        @endif
    </div>
@endif
