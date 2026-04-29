<x-main-layout>
    <section class="bg-[#f3f3f3] py-12">
        <div class="site-container px-5 lg:px-8 max-w-2xl">
            <div class="bg-[#f7f7f7] border border-[#e4e4e4] p-8 lg:p-10">
                <h1 class="text-[32px] font-semibold text-[#111] mb-4">Verify OTP</h1>
                <p class="text-[14px] text-[#555] leading-7 mb-6">
                    Enter the 6-digit OTP sent to <strong>{{ $email }}</strong>. OTP is valid for 5 minutes.
                </p>

                <x-flash-messages class="mb-4" />
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.otp.verify') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="otp" class="block text-[14px] text-[#1d1d1d] mb-2">OTP <span class="text-red-500">*</span></label>
                        <input id="otp" type="text" name="otp" value="{{ old('otp') }}" maxlength="6" required
                            class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 text-[16px] tracking-[0.25em] focus:outline-none focus:ring-1 focus:ring-black">
                        <x-input-error :messages="$errors->get('otp')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center justify-center h-11 min-w-[170px] px-8 bg-[#171717] text-white text-[14px] font-semibold hover:bg-black transition">
                            Verify OTP
                        </button>
                        <a href="{{ route('password.request') }}" class="text-[14px] text-[#555] no-underline hover:underline">Change email</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-main-layout>
