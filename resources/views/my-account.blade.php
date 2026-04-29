<x-main-layout>
    <section class="border-b border-[#dcdcdc]">
        <div class="site-container px-5 lg:px-8">
         <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 py-8">
                <div>
                    <h1 class="text-[30px] font-medium text-[#111] leading-tight">Dashboard</h1>
                    <p class="text-[16px] text-[#666] mt-2">Welcome back, {{ $user->first_name }} {{ $user->last_name }}</p>
                </div>
                @if($user->isTradeSeller() && $tradeUsage && !$tradeUsage['can_create'])
                    <button type="button" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 h-12 px-7 rounded-xl bg-[#d9d9d9] text-[#666] text-[16px] font-semibold cursor-not-allowed" title="Trade advert limit reached">
                        <span>+</span>
                        <span>New Listing</span>
                    </button>
                @else
                    <a href="{{ route('adverts.create') }}" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 h-12 px-7 rounded-xl bg-black text-white text-[16px] font-semibold no-underline hover:bg-[#161616]">
                        <span>+</span>
                        <span>New Listing</span>
                    </a>
                @endif
            </div>
        </div>
    </section>
    <section class="py-8">
        <div class="site-container px-5 lg:px-8">
            <x-flash-messages class="mb-6" />

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mt-8">
                <div class="rounded-2xl border border-[#dddddd] bg-white p-6 flex items-center justify-between">
                    <div>
                        <div class="text-[14px] text-[#666]">Total Views</div>
                        <div class="text-[24px] font-semibold leading-none mt-2">{{ $stats['views'] }}</div>
                    </div>
                    <span class="w-10 h-10 rounded-full bg-[#dceafe] text-[#2563eb] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"></circle></svg>
                    </span>
                </div>
                <div class="rounded-2xl border border-[#dddddd] bg-white p-6 flex items-center justify-between">
                    <div>
                        <div class="text-[14px] text-[#666]">Enquiries</div>
                        <div class="text-[24px] font-semibold leading-none mt-2">{{ $stats['enquiries'] }}</div>
                    </div>
                    <span class="w-10 h-10 rounded-full bg-[#d8f2df] text-[#16a34a] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8m-8 4h5m-9 5l3.6-3H19a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h1v3z"/></svg>
                    </span>
                </div>
                <div class="rounded-2xl border border-[#dddddd] bg-white p-6 flex items-center justify-between">
                    <div>
                        <div class="text-[14px] text-[#666]">{{ $user->isTradeSeller() && $tradeUsage ? 'Available Listings' : 'Active Listings' }}</div>
                        <div class="text-[24px] font-semibold leading-none mt-2">{{ $user->isTradeSeller() && $tradeUsage ? $tradeUsage['available_display'] : $stats['active'] }}</div>
                        @if($user->isTradeSeller() && $tradeUsage)
                            <div class="text-[12px] text-[#888] mt-2">
                                {{ $tradeUsage['unlimited'] ? 'Unlimited trade plan' : "{$tradeUsage['active_count']} used of {$tradeUsage['max']}" }}
                            </div>
                        @endif
                    </div>
                    <span class="w-10 h-10 rounded-full bg-[#f1ecd9] text-[#a5832f] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10"/></svg>
                    </span>
                </div>
                <div class="rounded-2xl border border-[#dddddd] bg-white p-6 flex items-center justify-between">
                    <div>
                        <div class="text-[14px] text-[#666]">Sold</div>
                        <div class="text-[24px] font-semibold leading-none mt-2">{{ $stats['sold'] }}</div>
                    </div>
                    <span class="w-10 h-10 rounded-full bg-[#eee0ff] text-[#9333ea] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 17l6-6 4 4 8-8M14 7h7v7"/></svg>
                    </span>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-2">
                @php
                    $tabs = [
                        'dashboard' => 'My Listings',
                        'profile' => 'Profile',
                        'edit-profile' => 'Edit Profile',
                        'invoices' => 'Invoices',
                        'settings' => 'Settings',
                    ];
                    if (auth()->user()->isTradeSeller()) {
                        $tabs = array_slice($tabs, 0, 3, true) + ['subscriptions' => 'My Subscriptions'] + array_slice($tabs, 3, null, true);
                    }
                @endphp
                @foreach($tabs as $key => $label)
                    <a href="{{ route('my-account', ['tab' => $key]) }}" class="h-10 px-4 rounded-xl border text-[14px] font-medium no-underline inline-flex items-center {{ $tab === $key ? 'bg-white border-[#d6d6d6] text-[#111] shadow-sm' : 'bg-[#ececec] border-transparent text-[#666] hover:bg-[#e6e6e6]' }}">{{ $label }}</a>
                @endforeach
            </div>

            @if($tab === 'dashboard')
                <div class="mt-8">
                    <div class="flex flex-wrap gap-2 mb-6">
                        @php
                            $statusTabs = [
                                'all' => 'All',
                                'active' => 'Active',
                                'sold' => 'Sold',
                                'expired' => 'Expired',
                            ];
                        @endphp
                        @foreach($statusTabs as $statusKey => $statusLabel)
                            <a href="{{ route('my-account', ['tab' => 'dashboard', 'listing' => $statusKey]) }}"
                               class="h-9 px-4 rounded-xl border no-underline text-[14px] inline-flex items-center {{ $listingFilter === $statusKey ? 'bg-white border-[#d5d5d5] text-[#111] shadow-sm font-semibold' : 'bg-[#ececec] border-transparent text-[#666]' }}">
                                {{ $statusLabel }} ({{ $statusCounts[$statusKey] ?? 0 }})
                            </a>
                        @endforeach
                    </div>

                    <div class="space-y-4">
                        @forelse($listings as $advert)
                            <div class="rounded-2xl border border-[#d9d9d9] bg-white p-5" x-data="{ openAction: false }">
                                <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-4">
                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <div class="w-full sm:w-28 h-52 sm:h-28 rounded-xl overflow-hidden bg-[#f2f2f2] shrink-0">
                                            @if($advert->mainImageUrl())
                                                <img src="{{ $advert->mainImageUrl() }}" alt="{{ $advert->title }}" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="text-[18px] font-semibold text-[#111]">{{ $advert->title }}</h3>
                                            <p class="text-[30px] sm:text-[40px] leading-none font-semibold mt-2">£{{ number_format((float) $advert->price, 0) }}</p>
                                            <div class="mt-3 flex flex-wrap items-center gap-x-6 gap-y-2 text-[14px] text-[#666]">
                                                <span class="inline-flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"></circle></svg> 0 views</span>
                                                <span class="inline-flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8m-8 4h5m-9 5l3.6-3H19a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h1v3z"/></svg> 0 enquiries</span>
                                                <span class="inline-flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> {{ $advert->created_at->diffForHumans() }}</span>
                                                <span>
                                                    @if($advert->expiry_date)
                                                        Expires {{ $advert->expiry_date->diffForHumans() }}
                                                    @else
                                                        No expiry
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="relative xl:ml-4">
                                        <div class="flex items-center justify-between sm:justify-start gap-3">
                                            <span class="px-4 h-7 rounded-lg text-[14px] inline-flex items-center capitalize {{ $advert->statusBadgeClass() }}">{{ $advert->status }}</span>
                                            <button type="button" @click="openAction = !openAction" class="w-11 h-11 rounded-lg border border-[#d8d8d8] text-[#444] hover:bg-[#f7f7f7]" aria-label="Listing actions">
                                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 5.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 8a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 8a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div x-cloak x-show="openAction" @click.away="openAction = false" class="absolute right-0 mt-2 w-full sm:w-52 bg-[#f7f7f7] border border-[#d8d8d8] rounded-xl shadow-md overflow-hidden z-20">
                                            <a href="{{ route('adverts.edit', $advert) }}" class="block px-4 py-3 text-[16px] text-[#222] no-underline hover:bg-[#eeeeee]">Edit Listing</a>
                                            <form method="POST" action="{{ route('adverts.mark-sold', $advert) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="w-full text-left px-4 py-3 text-[16px] text-[#222] hover:bg-[#eeeeee]">Mark as Sold</button>
                                            </form>
                                            <form method="POST" action="{{ route('adverts.pause', $advert) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="w-full text-left px-4 py-3 text-[16px] text-[#222] hover:bg-[#eeeeee]">
                                                    {{ $advert->status === \App\Models\Advert::STATUS_ACTIVE ? 'Put on Hold' : 'Resume Listing' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('adverts.destroy', $advert) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" onclick="return confirm('Delete this listing?')" class="w-full text-left px-4 py-3 text-[16px] text-red-500 hover:bg-[#eeeeee]">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-[#d9d9d9] bg-white py-14 text-center text-[#666] text-[16px]">No listings found.</div>
                        @endforelse
                    </div>

                    @if($listings->hasPages())
                        <div class="mt-6">{{ $listings->links() }}</div>
                    @endif
                </div>
            @elseif($tab === 'profile')
                <div class="mt-8 rounded-2xl border border-[#d9d9d9] bg-white p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-[22px] font-semibold text-[#111]">Profile Details</h2>
                        @if(auth()->user()->isPrivateSeller())
                            <a href="{{ route('seller.trade.packages') }}" class="h-10 px-6 rounded-xl bg-[#d4b160] text-[#111] text-[14px] font-semibold no-underline flex items-center hover:bg-[#c5a350]">Become a Trade Seller</a>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-[14px]">
                        <div><span class="text-[#666]">First Name:</span> <span class="text-[#111] font-medium">{{ $user->first_name }}</span></div>
                        <div><span class="text-[#666]">Last Name:</span> <span class="text-[#111] font-medium">{{ $user->last_name }}</span></div>
                        <div><span class="text-[#666]">Email:</span> <span class="text-[#111] font-medium">{{ $user->email }}</span></div>
                        <div><span class="text-[#666]">Phone:</span> <span class="text-[#111] font-medium">{{ $user->phone ?: '-' }}</span></div>
                        <div><span class="text-[#666]">Address:</span> <span class="text-[#111] font-medium">{{ $user->address ?: '-' }}</span></div>
                        <div><span class="text-[#666]">City:</span> <span class="text-[#111] font-medium">{{ $user->city ?: '-' }}</span></div>
                        <div><span class="text-[#666]">Postal Code:</span> <span class="text-[#111] font-medium">{{ $user->postal_code ?: '-' }}</span></div>
                        <div><span class="text-[#666]">Country:</span> <span class="text-[#111] font-medium">{{ $user->country ?: '-' }}</span></div>
                        <div><span class="text-[#666]">Account Type:</span> <span class="text-[#111] font-medium capitalize">{{ str_replace('_', ' ', $user->role) }}</span></div>
                    </div>
                </div>
            @elseif($tab === 'edit-profile')
                <div class="mt-8 rounded-2xl border border-[#d9d9d9] bg-white p-6">
                    <h2 class="text-[22px] font-semibold text-[#111] mb-6">Edit Profile</h2>
                    <form method="POST" action="{{ route('my-account.profile.update') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-[14px] text-[#444] mb-1">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="w-full h-11 border border-[#d1d1d1] rounded-lg px-3 text-[16px]">
                            @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[14px] text-[#444] mb-1">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="w-full h-11 border border-[#d1d1d1] rounded-lg px-3 text-[16px]">
                            @error('last_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[14px] text-[#444] mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full h-11 border border-[#d1d1d1] rounded-lg px-3 text-[16px]">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[14px] text-[#444] mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full h-11 border border-[#d1d1d1] rounded-lg px-3 text-[16px]">
                            @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[14px] text-[#444] mb-1">Address</label>
                            <input type="text" name="address" value="{{ old('address', $user->address) }}" class="w-full h-11 border border-[#d1d1d1] rounded-lg px-3 text-[16px]">
                            @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[14px] text-[#444] mb-1">City</label>
                            <input type="text" name="city" value="{{ old('city', $user->city) }}" class="w-full h-11 border border-[#d1d1d1] rounded-lg px-3 text-[16px]">
                            @error('city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[14px] text-[#444] mb-1">Postal Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="w-full h-11 border border-[#d1d1d1] rounded-lg px-3 text-[16px]">
                            @error('postal_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[14px] text-[#444] mb-1">Country</label>
                            <input type="text" name="country" value="{{ old('country', $user->country) }}" class="w-full h-11 border border-[#d1d1d1] rounded-lg px-3 text-[16px]">
                            @error('country')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2 pt-2">
                            <button type="submit" class="h-11 px-6 rounded-lg bg-black text-white text-[16px] font-semibold">Save Changes</button>
                        </div>
                    </form>
                </div>
            @elseif($tab === 'subscriptions' && $user->isTradeSeller())
                <div class="mt-8 rounded-2xl border border-[#d9d9d9] bg-white p-6">
                    <h2 class="text-[22px] font-semibold text-[#111] mb-6">My Subscriptions</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border border-[#d9d9d9]">
                            <thead class="bg-[#f6f6f6] text-[16px] text-[#333]">
                                <tr>
                                    <th class="px-4 py-3 border-r border-[#d9d9d9]">Level</th>
                                    <th class="px-4 py-3 border-r border-[#d9d9d9]">Fee</th>
                                    <th class="px-4 py-3 border-r border-[#d9d9d9]">Start Date</th>
                                    <th class="px-4 py-3 border-r border-[#d9d9d9]">End Date</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscriptions as $subscription)
                                    <tr class="text-[16px] text-[#222] border-t border-[#e2e2e2]">
                                        <td class="px-4 py-3 border-r border-[#e2e2e2]">{{ $subscription->level->name ?? '-' }}</td>
                                        <td class="px-4 py-3 border-r border-[#e2e2e2]">{{ $subscription->fee_label ?: '-' }}</td>
                                        <td class="px-4 py-3 border-r border-[#e2e2e2]">{{ $subscription->start_date ? $subscription->start_date->format('F j, Y') : '-' }}</td>
                                        <td class="px-4 py-3 border-r border-[#e2e2e2]">{{ $subscription->end_date ? $subscription->end_date->format('F j, Y') : 'Never' }}</td>
                                        <td class="px-4 py-3 capitalize">{{ $subscription->status }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-8 text-center text-[#666]">No subscription records.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif($tab === 'invoices')
                <div class="mt-8 rounded-2xl border border-[#d9d9d9] bg-white p-6">
                    <h2 class="text-[22px] font-semibold text-[#111] mb-6">Invoices</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border border-[#d9d9d9]">
                            <thead class="bg-[#f6f6f6] text-[16px] text-[#333]">
                                <tr>
                                    <th class="px-4 py-3 border-r border-[#d9d9d9]">Date</th>
                                    <th class="px-4 py-3 border-r border-[#d9d9d9]">Level</th>
                                    <th class="px-4 py-3 border-r border-[#d9d9d9]">Amount</th>
                                    <th class="px-4 py-3 border-r border-[#d9d9d9]">Status</th>
                                    <th class="px-4 py-3">Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr class="text-[16px] text-[#222] border-t border-[#e2e2e2]">
                                        <td class="px-4 py-3 border-r border-[#e2e2e2]">{{ ($order->ordered_at ?? $order->created_at)->format('F j, Y') }}</td>
                                        <td class="px-4 py-3 border-r border-[#e2e2e2]">{{ $order->level->name ?? '-' }}</td>
                                        <td class="px-4 py-3 border-r border-[#e2e2e2]">£{{ number_format((float) $order->total, 2) }}</td>
                                        <td class="px-4 py-3 border-r border-[#e2e2e2] capitalize">{{ $order->status }}</td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('invoices.download', $order) }}" class="text-[#0f172a] no-underline hover:underline">Download PDF</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-8 text-center text-[#666]">No invoices available.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($orders->hasPages())
                        <div class="mt-4">{{ $orders->links() }}</div>
                    @endif
                </div>
            @else
                <div class="mt-8 rounded-2xl border border-[#d9d9d9] bg-white p-6">
                    <h2 class="text-[32px] font-semibold text-[#111] mb-4">Settings</h2>
                    <p class="text-[16px] text-[#666]">Settings panel will be expanded here.</p>
                </div>
            @endif
        </div>
    </section>
</x-main-layout>
