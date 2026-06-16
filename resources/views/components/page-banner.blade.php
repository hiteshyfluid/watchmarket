@props(['title'])

<section class="relative bg-[#0b0b0b] text-white overflow-hidden">
    <div class="absolute inset-0" style="background-image: repeating-linear-gradient(135deg, rgba(255,255,255,0.05) 0px, rgba(255,255,255,0.05) 2px, transparent 2px, transparent 16px);"></div>
    <div class="relative site-container px-5 lg:px-8 py-14 sm:py-20 text-center">
        <h1 class="text-[28px] sm:text-[38px] font-semibold tracking-tight text-white">{{ $title }}</h1>
    </div>
</section>
