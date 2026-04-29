<x-main-layout>
    <section class="bg-[#f3f3f3] py-12">
        <div class="site-container px-5 lg:px-8 max-w-2xl">
            <div class="bg-[#f7f7f7] border border-[#e4e4e4] p-8 lg:p-10">
                <h1 class="text-[32px] font-semibold text-[#111] mb-4">Forgot Password</h1>
                <p class="text-[14px] text-[#555] leading-7 mb-6">
                    Enter your email address. We will send a 6-digit OTP to verify your identity.
                </p>

                <x-flash-messages class="mb-4" />
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-[14px] text-[#1d1d1d] mb-2">Email address <span class="text-red-500">*</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <button type="submit" class="inline-flex items-center justify-center h-11 min-w-[170px] px-8 bg-[#171717] text-white text-[14px] font-semibold hover:bg-black transition">
                            Send OTP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-main-layout>
