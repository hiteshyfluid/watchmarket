<x-main-layout>
    <section class="bg-[#f3f3f3] py-12">
        <div class="site-container px-5 lg:px-8 max-w-2xl">
            <div class="bg-[#f7f7f7] border border-[#e4e4e4] p-8 lg:p-10">
                <h1 class="text-[32px] font-semibold text-[#111] mb-4">Reset Password</h1>
                <p class="text-[14px] text-[#555] leading-7 mb-6">
                    Set a new password for <strong>{{ $email }}</strong>.
                </p>

                <x-flash-messages class="mb-4" />
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="relative">
                        <label for="password" class="block text-[14px] text-[#1d1d1d] mb-2">Password <span class="text-red-500">*</span></label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 pr-11 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                        <button type="button" data-toggle-password="password" class="absolute right-0 top-[29px] w-11 h-11 flex items-center justify-center text-[#888]" aria-label="Toggle password">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"></circle></svg>
                        </button>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="relative">
                        <label for="password_confirmation" class="block text-[14px] text-[#1d1d1d] mb-2">Confirm Password <span class="text-red-500">*</span></label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 pr-11 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                        <button type="button" data-toggle-password="password_confirmation" class="absolute right-0 top-[29px] w-11 h-11 flex items-center justify-center text-[#888]" aria-label="Toggle confirm password">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"></circle></svg>
                        </button>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div>
                        <button type="submit" class="inline-flex items-center justify-center h-11 min-w-[170px] px-8 bg-[#171717] text-white text-[14px] font-semibold hover:bg-black transition">
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        (function () {
            document.querySelectorAll('[data-toggle-password]').forEach((button) => {
                button.addEventListener('click', () => {
                    const inputId = button.getAttribute('data-toggle-password');
                    const input = document.getElementById(inputId);
                    if (!input) return;
                    input.type = input.type === 'password' ? 'text' : 'password';
                });
            });
        })();
    </script>
</x-main-layout>
