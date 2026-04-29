<x-main-layout>
    <div class="bg-gray-100 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-800">My Adverts</h1>
            <nav class="text-sm text-gray-500 mt-1">
                <a href="/" class="hover:underline">Home</a> &raquo; My Adverts
            </nav>
        </div>
    </div>

    <div class="py-10 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-flash-messages class="mb-6" />

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">
                        Adverts <span class="text-gray-400 font-normal text-base">({{ $adverts->total() }})</span>
                    </h2>
                    @if(!empty($tradeUsage))
                    <p class="text-sm text-gray-500 mt-1">
                        Available listings:
                        <span class="font-semibold text-gray-700">{{ $tradeUsage['available_display'] }}</span>
                        <span class="text-gray-400">
                            (Used: {{ $tradeUsage['display'] }})
                        </span>
                    </p>
                    @endif
                </div>

                @if(auth()->user()->isSeller())
                    @if(!empty($tradeUsage) && !$tradeUsage['can_create'])
                    <button type="button"
                        class="bg-gray-300 text-gray-600 px-6 py-2.5 font-bold uppercase tracking-widest text-xs cursor-not-allowed"
                        title="Trade advert limit reached">
                        + Create Advert
                    </button>
                    @else
                    <a href="{{ route('adverts.create') }}"
                       class="bg-black text-white px-6 py-2.5 font-bold uppercase tracking-widest text-xs hover:bg-gray-800 transition">
                        + Create Advert
                    </a>
                    @endif
                @else
                <a href="{{ route('seller.choose-account-type') }}"
                   class="bg-yellow-500 text-black px-6 py-2.5 font-bold uppercase tracking-widest text-xs hover:bg-yellow-400 transition">
                    Become a Seller
                </a>
                @endif
            </div>

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-900 text-white text-xs uppercase tracking-widest">
                        <tr>
                            <th class="px-4 py-3">Photo</th>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Brand / Model</th>
                            <th class="px-4 py-3 text-right">Price</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3">Condition</th>
                            <th class="px-4 py-3">Expires</th>
                            <th class="px-4 py-3 text-center">Phone</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($adverts as $advert)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                @if($advert->main_image)
                                    <img src="{{ $advert->mainImageUrl() }}" class="w-14 h-12 object-cover rounded" alt="">
                                @else
                                    <div class="w-14 h-12 bg-gray-200 rounded flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900 max-w-xs">{{ Str::limit($advert->title, 50) }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Listed {{ $advert->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                <span class="font-medium">{{ $advert->brand->name ?? '—' }}</span>
                                @if($advert->model)<br><span class="text-gray-400">{{ $advert->model->name }}</span>@endif
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">£{{ number_format($advert->price, 0) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $advert->statusBadgeClass() }}">
                                    {{ ucfirst($advert->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $advert->condition->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $advert->expiry_date ? $advert->expiry_date->format('d M Y') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($advert->show_phone)
                                    <span class="text-green-500" title="Phone visible">✓</span>
                                @else
                                    <span class="text-gray-300" title="Phone hidden">✗</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                @if($advert->status === \App\Models\Advert::STATUS_DRAFT && auth()->user()->isPrivateSeller())
                                <a href="{{ route('seller.private.packages', $advert) }}"
                                   class="text-blue-600 hover:underline text-xs font-medium">Complete Checkout</a>
                                @else
                                <a href="{{ route('adverts.edit', $advert) }}"
                                   class="text-blue-600 hover:underline text-xs font-medium">Edit</a>
                                @endif
                                @if(in_array($advert->status, [\App\Models\Advert::STATUS_ACTIVE, \App\Models\Advert::STATUS_PAUSED], true))
                                <form method="POST" action="{{ route('adverts.pause', $advert) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-amber-600 hover:underline text-xs">
                                        {{ $advert->status === \App\Models\Advert::STATUS_ACTIVE ? 'Put on Hold' : 'Resume' }}
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('adverts.destroy', $advert) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline text-xs"
                                        onclick="return confirm('Delete this advert?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <p class="font-medium text-gray-500">No adverts yet.</p>
                                    @if(auth()->user()->isSeller())
                                    <a href="{{ route('adverts.create') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">Create your first advert &rarr;</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($adverts->hasPages())
            <div class="mt-6">{{ $adverts->links() }}</div>
            @endif
        </div>
    </div>
</x-main-layout>
