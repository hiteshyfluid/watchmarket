<x-main-layout>
    <section class="bg-[#f3f3f3] py-10">
        <div class="site-container px-5 lg:px-8">
            <x-flash-messages class="mb-6" />
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-[#f7f7f7] border border-[#e4e4e4] p-8 lg:p-10">
                    <h2 class="text-[32px] font-semibold text-[#111] mb-8">Login</h2>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="login_email" class="block text-[14px] text-[#1d1d1d] mb-2">Username or email address <span class="text-red-500">*</span></label>
                            <input id="login_email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <label for="login_password" class="block text-[14px] text-[#1d1d1d] mb-2">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input id="login_password" type="password" name="password" required
                                    class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 pr-11 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                                <button type="button" data-toggle-password="login_password" class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-[#888]" aria-label="Toggle password">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"></circle></svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <label class="inline-flex items-center gap-3 text-[14px] text-[#555]">
                            <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-[#cfcfcf] text-black focus:ring-black">
                            <span>Remember me</span>
                        </label>

                        <div>
                            <button type="submit" class="inline-flex items-center justify-center h-11 min-w-[140px] px-8 bg-[#171717] text-white text-[14px] font-semibold hover:bg-black transition">
                                Log in
                            </button>
                        </div>

                        <div>
                            <a href="{{ route('password.request') }}" class="text-[14px] text-[#333] no-underline hover:underline">Lost your password?</a>
                        </div>
                    </form>
                </div>

                <div class="bg-[#f7f7f7] border border-[#e4e4e4] p-8 lg:p-10">
                    <h2 class="text-[32px] font-semibold text-[#111] mb-8">Register</h2>

                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="First Name *"
                                    class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                            </div>
                            <div>
                                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Last Name *"
                                    class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required placeholder="Phone *"
                                class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <div>
                            <input id="address" type="text" name="address" value="{{ old('address') }}" required placeholder="Address *"
                                class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <div>
                            <input id="city" type="text" name="city" value="{{ old('city') }}" required placeholder="City *"
                                class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                            <x-input-error :messages="$errors->get('city')" class="mt-2" />
                        </div>

                        <div>
                            <input id="postal_code" type="text" name="postal_code" value="{{ old('postal_code') }}" required placeholder="Postal Code *"
                                class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                            <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                        </div>

                        <div>
                            <select id="country" name="country" class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                                @foreach($countries as $country)
                                    <option value="{{ $country }}" {{ old('country', 'United Kingdom') == $country ? 'selected' : '' }}>{{ $country }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('country')" class="mt-2" />
                        </div>

                        <div>
                            <input id="register_email" type="email" name="email" value="{{ old('email') }}" required placeholder="Email address *"
                                class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="relative">
                            <input id="register_password" type="password" name="password" required placeholder="Password *"
                                class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 pr-11 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                            <button type="button" data-toggle-password="register_password" class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-[#888]" aria-label="Toggle password">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"></circle></svg>
                            </button>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Confirm Password *"
                                class="w-full h-11 border border-[#cfcfcf] bg-[#f7f7f7] px-4 pr-11 text-[16px] focus:outline-none focus:ring-1 focus:ring-black">
                            <button type="button" data-toggle-password="password_confirmation" class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-[#888]" aria-label="Toggle confirm password">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"></circle></svg>
                            </button>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <p class="text-[14px] leading-7 text-[#4f4f4f] pt-1">
                            Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our privacy policy.
                        </p>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center justify-center h-11 min-w-[140px] px-8 bg-[#171717] text-white text-[14px] font-semibold hover:bg-black transition">
                                Register
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
