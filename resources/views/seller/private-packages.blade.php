<x-main-layout>
    <section class="bg-gray-100 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900">
                Based on your selling price (£{{ number_format((float) $advert->price, 2) }}), choose one of the following packages:
            </h1>
        </div>
    </section>

    <section class="py-14 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-center sm:justify-end mb-6">
                <a href="{{ route('adverts.edit', $advert) }}"
                   class="inline-flex items-center gap-1.5 border border-gray-300 bg-white text-gray-700 px-4 py-2 text-xs font-bold uppercase tracking-widest hover:bg-gray-100 transition rounded">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Listing
                </a>
            </div>

            <x-flash-messages class="mb-8" />

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @forelse($levels as $level)
                <div class="bg-white border rounded p-8 shadow-sm hover:shadow-md transition flex flex-col">
                    <h2 class="text-2xl font-bold text-gray-900 uppercase">{{ $level->name }}</h2>
                    <div class="mt-4">
                        <p class="text-4xl font-bold text-gray-900">
                            £{{ number_format((float) ($level->initial_payment ?? 0), 2) }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">Pay as you go</p>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 mt-3">
                        @if($level->expirationLabel() !== '--')
                            Advert expires {{ lcfirst($level->expirationLabel()) }}
                        @else
                            Advert never expires
                        @endif
                    </p>
                    <div class="border-t my-6"></div>

                    <ul class="space-y-2 text-sm text-gray-600 flex-1">
                        @foreach(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', strip_tags((string) $level->description)))) as $line)
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 mt-0.5">✓</span>
                            <span>{{ $line }}</span>
                        </li>
                        @endforeach
                        @if($level->privatePriceRangeLabel())
                        <li class="flex items-start gap-2">
                            <span class="text-red-500 mt-0.5">✓</span>
                            <span>{{ $level->privatePriceRangeLabel() }}</span>
                        </li>
                        @endif
                    </ul>

                    <div class="mt-8">
                        <a href="{{ route('seller.private.checkout', [$advert, $level]) }}"
                           class="inline-flex items-center justify-center w-full bg-black text-white font-bold py-3 uppercase tracking-widest hover:bg-gray-800 transition no-underline">
                            Select Package
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full bg-white border rounded p-10 text-center text-gray-500">
                    No private packages are available for this advert price.
                </div>
                @endforelse
            </div>
        </div>
    </section>
</x-main-layout>
