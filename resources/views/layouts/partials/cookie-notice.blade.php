{{--
    Cookie notice. The site only sets strictly necessary cookies (session,
    CSRF, remember-me), which UK PECR exempts from consent, so this is an
    informational notice with a single acknowledge button. If analytics or
    marketing cookies are ever added, this must become an opt-in banner that
    blocks those scripts until the visitor accepts.
--}}
<div x-data="{
        show: false,
        init() { this.show = !document.cookie.split('; ').some(c => c.startsWith('wm_cookie_notice=')); },
        dismiss() {
            document.cookie = 'wm_cookie_notice=1; max-age=31536000; path=/; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : '');
            this.show = false;
        },
    }"
     x-show="show" x-cloak x-transition.opacity
     role="region" aria-label="Cookie notice"
     class="fixed inset-x-0 bottom-0 z-[60] p-4 sm:p-5 pointer-events-none">
    <div class="pointer-events-auto mx-auto max-w-3xl bg-[#111] text-white rounded-xl shadow-2xl px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6">
        <p class="text-[14px] leading-6 text-[#e5e5e5] flex-1">
            We use essential cookies to keep you signed in and keep the site secure. We don't use advertising or tracking cookies.
            <a href="{{ route('pages.cookies') }}" class="underline text-white hover:no-underline">Read our Cookie Policy</a>.
        </p>
        <button type="button" @click="dismiss()"
                class="shrink-0 h-10 px-6 rounded-lg bg-white text-[#111] text-[14px] font-semibold hover:bg-[#f0f0f0]">
            OK, got it
        </button>
    </div>
</div>
