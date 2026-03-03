<x-main-layout>
    @php
        $privateEditLock = $privateEditLock ?? false;
        $privatePriceRange = $privatePriceRange ?? null;
    @endphp

    <section x-data="editAdvertWizard()">
        <div class="bg-white border border-[#e1e1e1] rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-[#e5e5e5]">
                <div class="m-auto px-5 lg:px-8 max-w-4xl">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <template x-for="(item, idx) in steps" :key="idx">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold"
                                     :class="idx + 1 < step ? 'bg-[#22c55e] text-white' : (idx + 1 === step ? 'bg-black text-white' : 'bg-[#e8e8e8] text-[#666]')">
                                    <span x-show="idx + 1 >= step" x-text="idx + 1"></span>
                                    <span x-show="idx + 1 < step">✓</span>
                                </div>
                                <div>
                                    <div class="text-[14px] font-medium text-[#222]" x-text="item.title"></div>
                                    <div class="text-[10px] text-[#888]" x-text="item.subtitle"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="site-container px-5 lg:px-8 max-w-5xl">
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl mb-6 text-sm">
                        <strong>Please fix the errors below:</strong>
                        <ul class="list-disc list-inside mt-1 space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-main max-w-2xl m-auto">
                    <form id="edit-advert-form" action="{{ route('adverts.update', $advert) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
                        @csrf
                        @method('PUT')

                        <div x-show="step === 1" x-cloak x-data="{ selectedBrand: '{{ old('brand_id', $advert->brand_id) }}' }">
                            <h2 class="text-[28px] font-semibold text-[#111]">Watch Details</h2>
                            <p class="text-[16px] text-[#666] mt-2">Update your watch details</p>

                            <div class="mt-6 border border-[#d8d8d8] rounded-xl p-5 space-y-5 bg-[#fafafa]">
                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Title *</label>
                                    <input type="text" name="title" value="{{ old('title', $advert->title) }}" required {{ $privateEditLock ? 'readonly' : '' }}
                                        class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px] {{ $privateEditLock ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">Brand *</label>
                                        <select name="brand_id" @change="selectedBrand = $event.target.value" required {{ $privateEditLock ? 'disabled' : '' }}
                                            class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px] {{ $privateEditLock ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                            <option value="">Select brand</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id', $advert->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($privateEditLock)
                                            <input type="hidden" name="brand_id" value="{{ old('brand_id', $advert->brand_id) }}">
                                        @endif
                                    </div>

                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">Model *</label>
                                        <select name="model_id" required {{ $privateEditLock ? 'disabled' : '' }}
                                            class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px] {{ $privateEditLock ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                            <option value="">Select model</option>
                                            @foreach($allModels as $m)
                                                <option value="{{ $m->id }}" data-parent="{{ $m->parent_id }}" x-show="!selectedBrand || selectedBrand == '{{ $m->parent_id }}'" {{ old('model_id', $advert->model_id) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($privateEditLock)
                                            <input type="hidden" name="model_id" value="{{ old('model_id', $advert->model_id) }}">
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Reference Number</label>
                                    <input type="text" name="reference_number" value="{{ old('reference_number', $advert->reference_number) }}" placeholder="e.g., 126610LN"
                                        class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">Year *</label>
                                        <select name="year_id" required {{ $privateEditLock ? 'disabled' : '' }} class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px] {{ $privateEditLock ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                            <option value="">Select year</option>
                                            @foreach($years as $opt)
                                                <option value="{{ $opt->id }}" {{ old('year_id', $advert->year_id) == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($privateEditLock)
                                            <input type="hidden" name="year_id" value="{{ old('year_id', $advert->year_id) }}">
                                        @endif
                                    </div>

                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">Condition</label>
                                        <select name="condition_id" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                            <option value="">Select condition</option>
                                            @foreach($conditions as $opt)
                                                <option value="{{ $opt->id }}" {{ old('condition_id', $advert->condition_id) == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">Box *</label>
                                        <select name="box_id" required class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                            <option value="">Select box option</option>
                                            @foreach($boxes as $opt)
                                                <option value="{{ $opt->id }}" {{ old('box_id', $advert->box_id) == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">Papers *</label>
                                        <select name="paper_id" required class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                            <option value="">Select paper option</option>
                                            @foreach($papers as $opt)
                                                <option value="{{ $opt->id }}" {{ old('paper_id', $advert->paper_id) == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-show="step === 2" x-cloak>
                            <h2 class="text-[28px] font-semibold text-[#111]">Specifications</h2>
                            <p class="text-[16px] text-[#666] mt-2">Update technical details</p>

                            <div class="mt-6 border border-[#d8d8d8] rounded-xl p-5 space-y-5 bg-[#fafafa]">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">Movement</label>
                                        <select name="movement_id" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                            <option value="">Select type</option>
                                            @foreach($movements as $opt)
                                                <option value="{{ $opt->id }}" {{ old('movement_id', $advert->movement_id) == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">Case Size (mm)</label>
                                        <input type="text" name="case_size_mm" value="{{ old('case_size_mm', $advert->case_size_mm) }}" placeholder="e.g., 41" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Case Material</label>
                                    <select name="case_material_id" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                        <option value="">Select material</option>
                                        @foreach($caseMaterials as $opt)
                                            <option value="{{ $opt->id }}" {{ old('case_material_id', $advert->case_material_id) == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Dial Colour</label>
                                    <select name="dial_colour_id" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                        <option value="">Select dial colour</option>
                                        @foreach($dialColours as $opt)
                                            <option value="{{ $opt->id }}" {{ old('dial_colour_id', $advert->dial_colour_id) == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Bracelet Material</label>
                                    <select name="bracelet_material_id" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                        <option value="">Select bracelet material</option>
                                        @foreach($braceletMaterials as $opt)
                                            <option value="{{ $opt->id }}" {{ old('bracelet_material_id', $advert->bracelet_material_id) == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Service History</label>
                                    <textarea name="service_history" rows="4" placeholder="Describe any service history..." class="w-full border border-[#d0d0d0] rounded-lg px-3 py-2 text-[16px]">{{ old('service_history', $advert->service_history) }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Description *</label>
                                    <textarea name="description" rows="5" required placeholder="Add any additional details about the watch..." class="w-full border border-[#d0d0d0] rounded-lg px-3 py-2 text-[16px]">{{ old('description', strip_tags($advert->description)) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div x-show="step === 3" x-cloak>
                            <h2 class="text-[28px] font-semibold text-[#111]">Photos</h2>
                            <p class="text-[16px] text-[#666] mt-2">Manage current photos and upload more</p>

                            <div class="mt-6 border border-[#d8d8d8] rounded-xl p-5 space-y-5 bg-[#fafafa]">
                                @if($advert->main_image)
                                    <div>
                                        <p class="text-[13px] text-[#555] mb-2 font-semibold">Current Main Photo</p>
                                        <img src="{{ $advert->mainImageUrl() }}" class="w-36 h-28 object-cover rounded-lg border border-[#d7d7d7]" alt="Main photo">
                                    </div>
                                @endif

                                @if($advert->images->count())
                                    <div>
                                        <p class="text-[13px] text-[#555] mb-2 font-semibold">Current Gallery</p>
                                        <div class="flex flex-wrap gap-3">
                                            @foreach($advert->images as $img)
                                                <div class="relative group">
                                                    <img src="{{ $img->url() }}" class="w-24 h-20 object-cover rounded border border-[#d7d7d7]" alt="Gallery image">
                                                    <form method="POST" action="{{ route('adverts.images.destroy', [$advert, $img]) }}"
                                                        class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black/50 rounded transition">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-white text-xs font-bold" onclick="return confirm('Remove this image?')">Remove</button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Replace Main Photo</label>
                                    <input type="file" name="main_image" accept=".jpg,.jpeg,.png,.webp" class="block w-full text-[14px] text-[#444]">
                                </div>

                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Add More Gallery Photos</label>
                                    <input type="file" name="gallery[]" accept=".jpg,.jpeg,.png,.webp" multiple class="block w-full text-[14px] text-[#444]">
                                </div>
                            </div>
                        </div>

                        <div x-show="step === 4" x-cloak>
                            <h2 class="text-[28px] font-semibold text-[#111]">Pricing &amp; Location</h2>
                            <p class="text-[16px] text-[#666] mt-2">Update price and preferences</p>

                            <div class="mt-6 border border-[#d8d8d8] rounded-xl p-5 space-y-5 bg-[#fafafa]">
                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Asking Price (&pound;) *</label>
                                    <input type="number" name="price" id="price-input" value="{{ old('price', $advert->price) }}" step="0.01" min="{{ $privatePriceRange['min'] ?? 0 }}" @if($privatePriceRange) max="{{ $privatePriceRange['max'] }}" @endif required class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                    @if($privatePriceRange)
                                        <p class="text-[13px] text-amber-700 mt-2">Allowed pricing for this advert: &pound;{{ number_format($privatePriceRange['min'], 2) }} to &pound;{{ number_format($privatePriceRange['max'], 2) }}</p>
                                    @endif
                                </div>

                                <div class="space-y-3 border-b border-[#e2e2e2] pb-5">
                                    <label class="flex items-center justify-between gap-4">
                                        <span>
                                            <span class="block text-[16px] font-semibold text-[#111]">Price Negotiable</span>
                                            <span class="block text-[14px] text-[#666]">Let buyers know you're open to offers</span>
                                        </span>
                                        <span class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="price_negotiable" value="1" {{ old('price_negotiable', $advert->price_negotiable) ? 'checked' : '' }} class="sr-only peer">
                                            <span class="w-11 h-6 bg-[#e5e7eb] rounded-full transition peer-checked:bg-[#111]"></span>
                                            <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white border border-[#d1d5db] transition peer-checked:translate-x-5"></span>
                                        </span>
                                    </label>
                                    <label class="flex items-center justify-between gap-4">
                                        <span>
                                            <span class="block text-[16px] font-semibold text-[#111]">Accept Traders</span>
                                            <span class="block text-[14px] text-[#666]">Open to part-exchange deals</span>
                                        </span>
                                        <span class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="accept_traders" value="1" {{ old('accept_traders', $advert->accept_traders) ? 'checked' : '' }} class="sr-only peer">
                                            <span class="w-11 h-6 bg-[#e5e7eb] rounded-full transition peer-checked:bg-[#111]"></span>
                                            <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white border border-[#d1d5db] transition peer-checked:translate-x-5"></span>
                                        </span>
                                    </label>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">City</label>
                                        <input type="text" name="city" value="{{ old('city', $advert->city ?: auth()->user()->city) }}" placeholder="e.g., London" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                    </div>
                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">Postcode</label>
                                        <input type="text" name="postcode" value="{{ old('postcode', $advert->postcode ?: auth()->user()->postal_code) }}" placeholder="e.g., SW1" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Meeting Preference</label>
                                    <select name="meeting_preference_id" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                        <option value="">Select preference</option>
                                        @foreach($meetingPreferences as $opt)
                                            <option value="{{ $opt->id }}" {{ old('meeting_preference_id', $advert->meeting_preference_id) == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="inline-flex items-center gap-3 text-[16px] text-[#444]">
                                        <input type="checkbox" name="show_phone" value="1" {{ old('show_phone', $advert->show_phone) ? 'checked' : '' }} class="w-5 h-5 rounded border-[#bdbdbd] text-black focus:ring-black">
                                        <span>Show my phone number on this advert</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-between">
                            <button type="button" @click="prevStep" class="h-11 px-6 rounded-lg border border-[#d1d1d1] bg-white text-[16px] text-[#333]" :class="step === 1 ? 'opacity-50 cursor-not-allowed' : ''" :disabled="step === 1">Back</button>

                            <button type="button" x-show="step < 4" @click="nextStep" class="h-11 px-6 rounded-lg bg-[#171717] text-white text-[16px] font-semibold">Continue</button>
                            <button type="submit" x-show="step === 4" class="h-11 px-6 rounded-lg bg-[#d4b160] text-[#111] text-[16px] font-semibold">Update Listing</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <style>
        .form-main form label {
            font-size: 14px;
            font-weight: 500;
        }
    </style>

    <script>
        function editAdvertWizard() {
            return {
                step: 1,
                steps: [
                    { title: 'Watch Details', subtitle: 'Basic information' },
                    { title: 'Specifications', subtitle: 'Technical details' },
                    { title: 'Photos', subtitle: 'Images' },
                    { title: 'Pricing & Location', subtitle: 'Final step' },
                ],
                nextStep() {
                    if (!this.validateStep(this.step)) return;
                    if (this.step < 4) this.step++;
                },
                prevStep() {
                    if (this.step > 1) this.step--;
                },
                validateStep(current) {
                    const form = document.getElementById('edit-advert-form');
                    if (!form) return true;

                    if (current === 1) {
                        const required = ['title', 'brand_id', 'model_id', 'year_id', 'box_id', 'paper_id'];
                        for (const name of required) {
                            const el = form.querySelector(`[name="${name}"]`);
                            if (el && !el.disabled && !String(el.value || '').trim()) {
                                el.focus();
                                return false;
                            }
                        }
                    }

                    if (current === 2) {
                        const desc = form.querySelector('[name="description"]');
                        if (desc && !String(desc.value || '').trim()) {
                            desc.focus();
                            return false;
                        }
                    }

                    return true;
                }
            }
        }

        document.getElementById('edit-advert-form')?.addEventListener('submit', function (e) {
            @if($privatePriceRange)
                const priceInput = document.getElementById('price-input');
                const minPrice = {{ (float) $privatePriceRange['min'] }};
                const maxPrice = {{ (float) $privatePriceRange['max'] }};
                const currentPrice = parseFloat(priceInput?.value || '0');
                if (currentPrice < minPrice || currentPrice > maxPrice) {
                    alert(`You can set pricing only between £${minPrice.toFixed(2)} and £${maxPrice.toFixed(2)} for this package.`);
                    e.preventDefault();
                    return;
                }
            @endif
        });
    </script>
</x-main-layout>
