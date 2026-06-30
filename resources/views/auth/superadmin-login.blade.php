<x-main-layout>
    <section class="bg-[#f3f3f3] py-10">
        <div class="site-container px-5 lg:px-8">
            <x-flash-messages class="mb-6" />
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <div class="max-w-lg mx-auto">
                <div class="bg-[#f7f7f7] border border-[#e4e4e4] p-8 lg:p-10">
                    <h2 class="text-[32px] font-semibold text-[#111] mb-2">Administrator Login</h2>
                    <p class="text-[14px] text-[#888] mb-8">This portal is restricted to authorised administrators only.</p>

                    <form method="POST" action="{{ route('superadmin.login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="login_email" class="block text-[14px] text-[#1d1d1d] mb-2">Email address <span class="text-red-500">*</span></label>
                            <input id="login_email" type="email" name="login_email" value="{{ old('login_email') }}" required autofocus
                                class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                            <x-input-error :messages="$errors->get('login_email')" class="mt-2" />
                        </div>

                        <div>
                            <label for="login_password" class="block text-[14px] text-[#1d1d1d] mb-2">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input id="login_password" type="password" name="login_password" required
                                    class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 pr-11 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                                <button type="button" data-toggle-password="login_password" class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-[#888]" aria-label="Toggle password">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"></circle></svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('login_password')" class="mt-2" />
                        </div>

                        <div>
                            <button type="submit" class="inline-flex items-center justify-center h-11 min-w-[140px] px-8 bg-[#171717] text-white text-[14px] font-semibold hover:bg-black transition">
                                Sign In
                            </button>
                        </div>
                    </form>
                </div>
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
