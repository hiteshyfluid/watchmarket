<x-main-layout>
    <x-page-banner :title="$page->title" />

    <div class="site-container px-5 lg:px-8 py-12 sm:py-16">
        <div class="page-content max-w-[860px] mx-auto">
            {!! $page->content !!}
        </div>
    </div>

    <style>
        .page-content > :first-child { margin-top: 0; }
        .page-content h2 { font-size: 22px; font-weight: 600; margin-top: 2rem; margin-bottom: 0.75rem; color: #111; }
        .page-content h3 { font-size: 18px; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.5rem; color: #111; }
        .page-content p { margin-bottom: 1rem; line-height: 1.7; color: #3f3f3f; }
        .page-content ul { margin: 0 0 1rem 1.25rem; list-style: disc; }
        .page-content li { margin-bottom: 0.4rem; line-height: 1.6; color: #3f3f3f; }
        .page-content strong { color: #111; }
        .page-content a { color: #b8924a; text-decoration: underline; }
    </style>
</x-main-layout>
