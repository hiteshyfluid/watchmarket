<x-main-layout>
    <div x-data="helpCentre()" class="min-h-screen bg-[#f2f2f2]">

        {{-- Hero --}}
        <section class="py-20 px-4 text-center">
            <p class="text-[11px] font-semibold tracking-[0.3em] uppercase text-[#999] mb-5">WatchMarket Help Centre</p>
            <h1 class="text-[52px] sm:text-[64px] font-black text-[#111] leading-none mb-5">How can we help?</h1>
            <p class="text-[16px] text-[#666] max-w-[500px] mx-auto mb-12 leading-relaxed">
                Find answers about buying, selling, and staying safe on the UK's
                trusted marketplace for luxury pre-owned watches.
            </p>
        </section>

        {{-- Browse by Topic --}}
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
            <p class="text-[10px] font-bold tracking-[0.3em] uppercase text-[#999] mb-5">Browse by Topic</p>
            <div class="grid grid-cols-2 md:grid-cols-3">
                @php
                $cards = [
                    ['id' => 'about',    'name' => 'About WatchMarket',    'count' => 3,
                     'bg' => 'radial-gradient(ellipse at 65% 35%, #2a2a42 0%, #0d0d18 70%)'],
                    ['id' => 'buying',   'name' => 'Buying a Watch',       'count' => 7,
                     'bg' => 'radial-gradient(ellipse at 40% 60%, #252525 0%, #0a0a0a 70%)'],
                    ['id' => 'selling',  'name' => 'Selling a Watch',      'count' => 5,
                     'bg' => 'radial-gradient(ellipse at 55% 45%, #2d2620 0%, #0f0b08 70%)'],
                    ['id' => 'trade',    'name' => 'Trade Accounts',       'count' => 3,
                     'bg' => 'radial-gradient(ellipse at 35% 55%, #1e1e2a 0%, #080808 70%)'],
                    ['id' => 'safety',   'name' => 'Safety & Fraud',       'count' => 4,
                     'bg' => 'radial-gradient(ellipse at 60% 40%, #1e2028 0%, #0a0a10 70%)'],
                    ['id' => 'accounts', 'name' => 'Accounts & Technical', 'count' => 4,
                     'bg' => 'radial-gradient(ellipse at 45% 50%, #222222 0%, #0c0c0c 70%)'],
                ];
                @endphp
                @foreach($cards as $card)
                <div @click="setActiveTopic('{{ $card['id'] }}')"
                     :class="activeTopic === '{{ $card['id'] }}' ? 'ring-[3px] ring-inset ring-[#d4b160]' : 'ring-0'"
                     class="relative h-44 overflow-hidden cursor-pointer group border border-[#3a3a3a]"
                     style="background: {{ $card['bg'] }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition duration-300"></div>
                    <div class="absolute bottom-0 left-0 p-4">
                        <p class="text-[10px] tracking-[0.18em] uppercase text-[#bbb] mb-1.5">{{ $card['count'] }} Articles</p>
                        <h3 class="text-white font-semibold text-[15px] leading-tight">{{ $card['name'] }}</h3>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-[#d4b160]"
                         :class="activeTopic === '{{ $card['id'] }}' ? 'opacity-0' : 'scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left'"></div>
                </div>
                @endforeach
            </div>

            {{-- View all / active indicator --}}
            <div class="mt-4 flex items-center gap-2 text-[13px] text-[#888]" x-show="activeTopic !== null" x-cloak>
                <button @click="activeTopic = null; openItem = null"
                        class="text-[#d4b160] hover:underline font-medium">
                    ← View all topics
                </button>
                <span>·</span>
                <span x-text="topics.find(t => t.id === activeTopic)?.name"></span>
            </div>
        </div>

        {{-- FAQ Accordion Sections --}}
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">

            <template x-for="topic in visibleTopics" :key="topic.id">
                <div :id="topic.id" class="mb-16 scroll-mt-8">
                    <p class="text-[10px] font-bold tracking-[0.25em] uppercase text-[#d4b160] mb-2"
                       x-text="topic.questions.length + ' Questions'"></p>
                    <h2 class="text-[28px] font-bold text-[#111] mb-6" x-text="topic.name"></h2>

                    <template x-for="(item, idx) in topic.questions" :key="idx">
                        <div class="mb-2 border-l-[3px] border-[#d4b160] bg-white">
                            <button @click="toggleItem(topic.id, idx)"
                                    class="w-full flex items-center justify-between px-5 py-4 text-left">
                                <span class="text-[15px] text-[#222] font-medium pr-4" x-text="item.q"></span>
                                <svg :class="isOpen(topic.id, idx) ? 'rotate-180' : ''"
                                     class="w-4 h-4 text-[#999] shrink-0 transition-transform duration-200"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="isOpen(topic.id, idx)"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="px-5 pb-5 border-t border-[#f0f0f0]">
                                <p class="text-[14px] text-[#555] leading-[1.7] pt-4" x-text="item.a"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <div x-show="visibleTopics.length === 0" class="py-20 text-center text-[#999] text-[16px]">
                No results found for "<span x-text="search" class="font-semibold text-[#555]"></span>".
            </div>
        </div>

        {{-- Still have questions? --}}
        <div class="border-t border-[#e8e8e8] bg-white">
            <div class="max-w-2xl mx-auto px-6 py-12">
                <h2 class="text-[22px] font-bold text-[#111] mb-2">Still have questions?</h2>
                <p class="text-[14px] text-[#555] leading-relaxed mb-5">
                    Our team is happy to help. Visit our Contact Us page and we'll get back to you as soon as possible.
                </p>
                <a href="{{ route('contact.show') }}"
                   class="text-[11px] font-bold tracking-[0.2em] uppercase text-[#d4b160] hover:underline no-underline">
                    Contact Us →
                </a>
            </div>
        </div>
    </div>

    <script>
    function helpCentre() {
        return {
            search: '',
            activeTopic: null,
            openItem: null,

            setActiveTopic(id) {
                this.activeTopic = id;
                this.openItem = null;
                this.$nextTick(() => {
                    const el = document.getElementById(id);
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            },

            toggleItem(topicId, idx) {
                const key = topicId + '_' + idx;
                this.openItem = this.openItem === key ? null : key;
            },

            isOpen(topicId, idx) {
                return this.openItem === topicId + '_' + idx;
            },

            get visibleTopics() {
                const base = this.filteredTopics;
                if (!this.activeTopic || this.search.trim()) return base;
                return base.filter(t => t.id === this.activeTopic);
            },

            get filteredTopics() {
                if (!this.search.trim()) return this.topics;
                const q = this.search.toLowerCase().trim();
                return this.topics
                    .map(topic => ({
                        ...topic,
                        questions: topic.questions.filter(item =>
                            item.q.toLowerCase().includes(q) || item.a.toLowerCase().includes(q)
                        )
                    }))
                    .filter(topic => topic.questions.length > 0);
            },

            topics: [
                {
                    id: 'about',
                    name: 'About WatchMarket',
                    questions: [
                        {
                            q: 'What is WatchMarket?',
                            a: "WatchMarket is the UK's online marketplace for buying and selling luxury pre-owned watches. We connect private sellers and trade dealers with buyers looking for timepieces from brands like Rolex, Omega, Patek Philippe, Breitling, and hundreds more."
                        },
                        {
                            q: 'Does WatchMarket buy or sell watches itself?',
                            a: "No. We're a marketplace — we provide the platform for buyers and sellers to connect. We don't hold stock, handle payment, or take a commission on sales."
                        },
                        {
                            q: 'Is WatchMarket free to use?',
                            a: "Searching and browsing listings is completely free. Sellers pay a listing fee to advertise their watch. Some listing tiers are free. View our pricing page for full details."
                        },
                    ]
                },
                {
                    id: 'buying',
                    name: 'Buying a Watch',
                    questions: [
                        {
                            q: 'How do I find a watch?',
                            a: 'Use the search bar at the top of the site. You can filter by brand, model, price range, and distance from your location. You can also browse by brand from our brands page.'
                        },
                        {
                            q: 'How do I contact a seller?',
                            a: 'Each listing has a contact button. You can message the seller directly through the site to ask questions, arrange a viewing, or agree terms.'
                        },
                        {
                            q: 'How do I pay for a watch?',
                            a: "Payment is agreed directly between you and the seller. WatchMarket does not process payments. We strongly recommend using a secure, traceable payment method — bank transfer is common for higher-value watches. Avoid cash for high-value transactions unless you're meeting in person at a verified business premises."
                        },
                        {
                            q: 'Can I view a watch in person before buying?',
                            a: "That's entirely up to you and the seller to arrange. For private sales, we recommend meeting in a safe, public place or at the seller's verified premises. Never agree to pay before inspecting the watch."
                        },
                        {
                            q: 'Is there a buyer protection scheme?',
                            a: "WatchMarket is an advertising platform — we don't provide buyer protection or escrow services. Due diligence is the buyer's responsibility. See our buying safety tips for more information."
                        },
                        {
                            q: 'What should I check before buying a pre-owned watch?',
                            a: "At minimum: verify the serial number, check the paperwork (box and papers where available), inspect the watch in person, and consider having it independently authenticated if it's a high-value piece. Be cautious if a deal seems too good to be true."
                        },
                        {
                            q: 'What is a Trade Seller?',
                            a: "Trade sellers are registered watch dealers or businesses listing on WatchMarket. You can filter search results to show trade sellers only. Trade sellers are typically subject to consumer protection regulations — always check their returns and warranty policies before purchasing."
                        },
                    ]
                },
                {
                    id: 'selling',
                    name: 'Selling a Watch',
                    questions: [
                        {
                            q: 'How do I list my watch?',
                            a: 'Register for an account, then follow the three steps: create your listing, add photos and details, and publish. Buyers will contact you directly.'
                        },
                        {
                            q: 'How much does it cost to list?',
                            a: 'We offer a range of listing options including a free tier. Visit our pricing page for current rates and details on featured or boosted listing options.'
                        },
                        {
                            q: 'How do I edit or delete my listing?',
                            a: 'Log in to your account and navigate to your listings. You can update photos, description, price, and status, or delete the advert entirely at any time.'
                        },
                        {
                            q: 'When should I hand over the watch?',
                            a: "Only once cleared funds are in your bank account. Do not release the watch based on a payment screenshot, pending transfer, or third-party confirmation. If a buyer is pressuring you to ship before payment clears, treat it as a red flag."
                        },
                        {
                            q: 'Can I sell multiple watches?',
                            a: 'Yes. You can list as many watches as you like, subject to your account tier. Trade accounts may have different listing limits — contact us for details.'
                        },
                    ]
                },
                {
                    id: 'trade',
                    name: 'Trade Accounts',
                    questions: [
                        {
                            q: "I'm a watch dealer — can I list my stock on WatchMarket?",
                            a: 'Yes. WatchMarket supports both private and trade sellers. Trade accounts allow you to list multiple watches and are displayed with a "Trade Seller" badge so buyers know they\'re dealing with a business.'
                        },
                        {
                            q: 'What are the benefits of a trade account?',
                            a: 'Trade accounts offer bulk listing options, a verified "Trade Seller" badge on all your listings, and dedicated support. Contact us for more details on trade account features and pricing.'
                        },
                        {
                            q: 'How do I upgrade to or apply for a trade account?',
                            a: 'Contact us to discuss trade account options. Our team will walk you through the application process and available plans.'
                        },
                    ]
                },
                {
                    id: 'safety',
                    name: 'Safety & Fraud',
                    questions: [
                        {
                            q: 'How do I report a suspicious or fake listing?',
                            a: 'If you spot a listing you believe to be fraudulent, misleading, or advertising a counterfeit watch, please use the report link on the listing page, or contact us directly. We investigate all reports and will remove listings that breach our terms.'
                        },
                        {
                            q: 'What are the warning signs of a scam?',
                            a: "Watch out for: prices significantly below market value, sellers unwilling to meet in person or provide additional photos, requests to pay via unusual methods (gift cards, crypto, international wire to an unknown party), buyers asking you to ship before payment clears, and pressure to complete quickly or bypass the platform."
                        },
                        {
                            q: "What do I do if I think I've been scammed?",
                            a: "Contact Action Fraud (the UK's national fraud reporting centre) at actionfraud.police.uk or call 0300 123 2040. Also notify WatchMarket so we can remove the listing and warn other users."
                        },
                        {
                            q: 'Does WatchMarket verify sellers?',
                            a: 'We encourage all users to conduct their own due diligence before completing any transaction. Trade sellers go through a verification process. We continuously monitor listings for suspicious activity.'
                        },
                    ]
                },
                {
                    id: 'accounts',
                    name: 'Accounts & Technical',
                    questions: [
                        {
                            q: 'How do I create an account?',
                            a: 'Click Register at the top of the site and complete the sign-up form. Once registered, you can list watches, save searches, and manage your adverts.'
                        },
                        {
                            q: "I've forgotten my password — how do I reset it?",
                            a: "Use the \"Forgotten password\" link on the login page and follow the email instructions. If you don't receive the reset email, check your spam folder or contact us."
                        },
                        {
                            q: "Can I save watches I'm interested in?",
                            a: "Yes — use the wishlist feature to save listings for later. You'll need to be logged in to access this feature."
                        },
                        {
                            q: 'How do I close my account?',
                            a: "Contact us and we'll process your account closure request. Please note that any active listings will be removed when your account is closed."
                        },
                    ]
                },
            ],
        };
    }
    </script>
</x-main-layout>
