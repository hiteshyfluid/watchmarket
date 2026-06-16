<x-main-layout>
    <x-page-banner title="Contact Us" />

    <div class="site-container px-5 lg:px-8 py-12 sm:py-16">
        <div class="max-w-[760px] mx-auto">
            <h2 class="text-[22px] sm:text-[26px] font-bold text-[#111] uppercase tracking-tight mb-6">Contact Us</h2>

            <x-flash-messages class="mb-6" />

            <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                @csrf

                <div>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Your Name *"
                           class="w-full border border-[#dcdcdc] rounded-lg px-4 py-3 text-[15px] placeholder:text-[#d4b160] focus:outline-none focus:ring-2 focus:ring-[#d4b160]">
                    @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Your Email"
                           class="w-full border border-[#dcdcdc] rounded-lg px-4 py-3 text-[15px] placeholder:text-[#d4b160] focus:outline-none focus:ring-2 focus:ring-[#d4b160]">
                    @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Your Phone *"
                           class="w-full border border-[#dcdcdc] rounded-lg px-4 py-3 text-[15px] placeholder:text-[#d4b160] focus:outline-none focus:ring-2 focus:ring-[#d4b160]">
                    @error('phone')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Your Title *"
                           class="w-full border border-[#dcdcdc] rounded-lg px-4 py-3 text-[15px] placeholder:text-[#d4b160] focus:outline-none focus:ring-2 focus:ring-[#d4b160]">
                    @error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <textarea name="message" rows="6" placeholder="Message *"
                              class="w-full border border-[#dcdcdc] rounded-lg px-4 py-3 text-[15px] placeholder:text-[#d4b160] focus:outline-none focus:ring-2 focus:ring-[#d4b160]">{{ old('message') }}</textarea>
                    @error('message')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="bg-black text-white px-8 py-3 rounded-lg text-[15px] font-semibold hover:bg-[#1a1a1a] transition">
                    Send Message
                </button>
            </form>
        </div>
    </div>
</x-main-layout>
