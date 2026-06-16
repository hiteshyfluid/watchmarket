@extends('layouts.admin')
@section('title', 'Contact Settings')

@section('content')
<div class="max-w-3xl space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Contact Settings</h2>
        <p class="text-sm text-gray-500 mt-1">Choose which email address(es) receive submissions from the public Contact Us form.</p>
    </div>

    <form method="POST" action="{{ route('admin.contact-settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="bg-white rounded-lg shadow-sm p-6 space-y-3">
            <label class="block text-sm font-medium text-gray-700">Recipient Email(s)</label>
            <textarea name="emails" rows="5"
                      class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                      placeholder="One email per line, e.g.&#10;wp@fluidlabs.co.uk&#10;sales@watchmarket.co.uk">{{ old('emails', $emails) }}</textarea>
            <p class="text-xs text-gray-500">Add one email per line (or separate with commas). Every inquiry will be sent to all addresses listed here.</p>
            @error('emails')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </section>

        <div class="pt-2">
            <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded text-sm font-medium hover:bg-gray-700">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
