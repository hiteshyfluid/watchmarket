<x-main-layout>
    <div class="min-h-screen bg-[#f2f2f2]">

        {{-- Hero --}}
        <section class="bg-[#f2f2f2] py-20 px-4 text-center">
            <p class="text-[11px] font-semibold tracking-[0.3em] uppercase text-[#999] mb-5">Community Safety</p>
            <h1 class="text-[52px] sm:text-[64px] font-black text-[#111] leading-none mb-5">Report a Listing</h1>
            <p class="text-[16px] text-[#666] max-w-[520px] mx-auto leading-relaxed">
                If you've spotted a listing you believe to be fraudulent, counterfeit, or advertising a stolen watch, please let us know. Every report is reviewed by our team. You could save another buyer from a serious loss.
            </p>
        </section>

        <div class="border-t border-[#e8e8e8]"></div>

        {{-- Form --}}
        <div class="max-w-2xl mx-auto px-6 py-12">

            <x-flash-messages class="mb-8" />

            <form method="POST" action="{{ route('report-listing.store') }}" class="space-y-0" x-data="{ issueType: '' }">
                @csrf

                {{-- Listing URL --}}
                <div class="py-7 border-b border-[#e8e8e8]">
                    <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-[#999] mb-3">
                        Listing URL or ID <span class="text-red-500">*</span> is not required — but helpful
                    </label>
                    <input type="text" name="listing_url" value="{{ old('listing_url') }}"
                           placeholder="e.g. https://watchmarket.co.uk/listing/12345"
                           class="w-full bg-transparent border-0 border-b border-[#d0d0d0] pb-2 text-[15px] text-[#333] placeholder-[#bbb] outline-none focus:border-[#d4b160] transition-colors">
                    @error('listing_url')<p class="text-xs text-red-500 mt-2">{{ $message }}</p>@enderror
                </div>

                {{-- Issue Type --}}
                <div class="py-7 border-b border-[#e8e8e8]">
                    <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-[#999] mb-4">
                        Type of Issue <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @php
                        $issues = [
                            ['value' => 'counterfeit',    'label' => 'Counterfeit / Fake Watch',    'desc' => 'The watch appears to be a replica or non-genuine timepiece'],
                            ['value' => 'stolen',         'label' => 'Stolen Watch',                'desc' => 'You have reason to believe the watch is stolen property'],
                            ['value' => 'fraudulent',     'label' => 'Fraudulent Listing',          'desc' => 'The listing is deceptive or the seller is misrepresenting themselves'],
                            ['value' => 'misrepresented', 'label' => 'Misrepresented Condition',    'desc' => 'The watch condition, history, or specifications are inaccurate'],
                            ['value' => 'scam_seller',    'label' => 'Suspected Scam Seller',       'desc' => "The seller's behaviour raises serious red flags"],
                            ['value' => 'other',          'label' => 'Other Issue',                 'desc' => 'Another concern not covered by the above categories'],
                        ];
                        @endphp
                        @foreach($issues as $issue)
                        <label class="relative flex flex-col gap-1 border rounded p-4 cursor-pointer transition-colors duration-150"
                               :class="issueType === '{{ $issue['value'] }}' ? 'border-[#d4b160] bg-[#fdf8ee]' : 'border-[#ddd] bg-white hover:border-[#bbb]'">
                            <input type="radio" name="issue_type" value="{{ $issue['value'] }}"
                                   x-model="issueType"
                                   {{ old('issue_type') === $issue['value'] ? 'checked' : '' }}
                                   class="sr-only">
                            <span class="text-[14px] font-semibold text-[#111]">{{ $issue['label'] }}</span>
                            <span class="text-[12px] text-[#777] leading-snug">{{ $issue['desc'] }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('issue_type')<p class="text-xs text-red-500 mt-2">{{ $message }}</p>@enderror
                </div>

                {{-- Description --}}
                <div class="py-7 border-b border-[#e8e8e8]">
                    <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-[#999] mb-3">
                        Describe the Issue <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" rows="5" maxlength="5000"
                              placeholder="Please provide as much detail as possible — what made you suspicious, what you observed, any relevant context..."
                              class="w-full bg-transparent border-0 border-b border-[#d0d0d0] text-[15px] text-[#333] placeholder-[#bbb] outline-none focus:border-[#d4b160] transition-colors resize-none pb-2">{{ old('description') }}</textarea>
                    @error('description')<p class="text-xs text-red-500 mt-2">{{ $message }}</p>@enderror
                </div>

                {{-- Serial Number --}}
                <div class="py-7 border-b border-[#e8e8e8]">
                    <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-[#999] mb-3">
                        Watch Serial Number <span class="text-[#bbb] normal-case tracking-normal text-[11px]">(optional)</span>
                    </label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}"
                           placeholder="e.g. SF12345 — found on case back or between lugs"
                           class="w-full bg-transparent border-0 border-b border-[#d0d0d0] pb-2 text-[15px] text-[#333] placeholder-[#bbb] outline-none focus:border-[#d4b160] transition-colors">
                    @error('serial_number')<p class="text-xs text-red-500 mt-2">{{ $message }}</p>@enderror
                </div>

                {{-- Name + Email --}}
                <div class="py-7 border-b border-[#e8e8e8]">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-[#999] mb-3">
                                Your Name <span class="text-[#bbb] normal-case tracking-normal text-[11px]">(optional)</span>
                            </label>
                            <input type="text" name="reporter_name" value="{{ old('reporter_name') }}"
                                   placeholder="Full name"
                                   class="w-full bg-transparent border-0 border-b border-[#d0d0d0] pb-2 text-[15px] text-[#333] placeholder-[#bbb] outline-none focus:border-[#d4b160] transition-colors">
                            @error('reporter_name')<p class="text-xs text-red-500 mt-2">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-[0.2em] uppercase text-[#999] mb-3">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="reporter_email" value="{{ old('reporter_email') }}"
                                   placeholder="your@email.com"
                                   class="w-full bg-transparent border-0 border-b border-[#d0d0d0] pb-2 text-[15px] text-[#333] placeholder-[#bbb] outline-none focus:border-[#d4b160] transition-colors">
                            @error('reporter_email')<p class="text-xs text-red-500 mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <p class="text-[12px] text-[#999] mt-5">Your contact details are used only to follow up on this report. We won't share them with the seller.</p>
                </div>

                {{-- Submit --}}
                <div class="pt-7">
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-11 px-8 bg-[#171717] text-white text-[14px] font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21l1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/>
                        </svg>
                        Submit Report
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-main-layout>
