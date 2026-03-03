<x-main-layout>
    @php
        $oldUploadedPhotos = array_values(array_filter((array) old('uploaded_photos', [])));
        $photoPreviewSeed = collect($oldUploadedPhotos)
            ->map(fn ($path) => [
                'id' => $path,
                'path' => $path,
                'url' => str_starts_with(ltrim((string) $path, '/'), 'images/')
                    ? asset(ltrim((string) $path, '/'))
                    : \Illuminate\Support\Facades\Storage::disk('public')->url($path),
            ])
            ->values();
        $popularBrands = $brands->where('is_popular', true)->values();
        $otherBrands = $brands->where('is_popular', false)->values();
    @endphp
    <section class="" x-data="createAdvertWizard()">
        
           

            <div class="bg-white border border-[#e1e1e1] rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-[#e5e5e5]">
                    <div class="m-auto px-5 lg:px-8 max-w-4xl">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <template x-for="(item, idx) in steps" :key="idx">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold"
                                     :class="idx + 1 < step ? 'bg-[#22c55e] text-white' : (idx + 1 === step ? 'bg-black text-white' : 'bg-[#e8e8e8] text-[#666]')">
                                    <span x-show="idx + 1 >= step" x-text="idx + 1"></span>
                                    <span x-show="idx + 1 < step">?</span>
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
                <form id="create-advert-form" action="{{ route('adverts.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8" @submit="syncPhotosInput()">
                    @csrf

                    <div x-show="step === 1" x-cloak>
                        <h2 class="text-[28px] font-semibold text-[#111]">Watch Details</h2>
                        <p class="text-[16px] text-[#666] mt-2">Tell us about the watch you're selling</p>

                        <div class="mt-6 border border-[#d8d8d8] rounded-xl p-5 space-y-5 bg-[#fafafa]">
                            <div>
                                <label class="block text-[16px] font-semibold text-[#111] mb-2">Title *</label>
                                <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g., Rolex Submariner Date"
                                    class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Brand *</label>
                                    <input type="hidden" name="brand_id" :value="selectedBrand">
                                    <div class="relative">
                                        <button type="button"
                                            @click="brandOpen = !brandOpen; if (brandOpen) { $nextTick(() => $refs.brandSearchInput?.focus()); }"
                                            class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-left text-[14px] bg-white flex items-center justify-between">
                                            <span x-text="selectedBrandName() || 'Select brand'" class="truncate"></span>
                                            <span>⌄</span>
                                        </button>
                                        <div x-show="brandOpen" @click.outside="brandOpen = false" x-cloak class="absolute z-20 mt-1 w-full bg-white border border-[#d0d0d0] rounded-lg shadow-lg">
                                            <div class="p-2 border-b border-[#efefef]">
                                                <input x-ref="brandSearchInput" type="text" x-model="brandSearch" placeholder="Search brand..."
                                                    class="w-full h-9 border border-[#d0d0d0] rounded px-2 text-[14px]">
                                            </div>
                                            <div class="max-h-56 overflow-y-auto p-1">
                                                @if($popularBrands->isNotEmpty())
                                                    <div class="px-2 py-1 text-[12px] font-semibold text-[#777] uppercase">Popular</div>
                                                    @foreach($popularBrands as $brand)
                                                        <button type="button"
                                                            x-show="!brandSearch || '{{ strtolower($brand->name) }}'.includes(brandSearch.toLowerCase())"
                                                            @click="selectBrand('{{ $brand->id }}'); brandOpen = false"
                                                            class="w-full text-left px-2 py-1.5 rounded text-[14px] hover:bg-[#f3f3f3]">
                                                            {{ $brand->name }}
                                                        </button>
                                                    @endforeach
                                                @endif

                                                @if($otherBrands->isNotEmpty())
                                                    <div class="px-2 pt-2 pb-1 text-[12px] font-semibold text-[#777] uppercase">Other Brands</div>
                                                    @foreach($otherBrands as $brand)
                                                        <button type="button"
                                                            x-show="!brandSearch || '{{ strtolower($brand->name) }}'.includes(brandSearch.toLowerCase())"
                                                            @click="selectBrand('{{ $brand->id }}'); brandOpen = false"
                                                            class="w-full text-left px-2 py-1.5 rounded text-[14px] hover:bg-[#f3f3f3]">
                                                            {{ $brand->name }}
                                                        </button>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Model *</label>
                                    <input type="hidden" name="model_id" :value="selectedModel">
                                    {{-- <p x-show="!selectedBrand" class="text-[12px] text-[#777] mb-2">Select brand first to see models.</p> --}}
                                    <div class="relative">
                                        <button type="button"
                                            @click="if (!selectedBrand) return; modelOpen = !modelOpen; if (modelOpen) { $nextTick(() => $refs.modelSearchInput?.focus()); }"
                                            :disabled="!selectedBrand"
                                            class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-left text-[14px] bg-white flex items-center justify-between disabled:bg-[#f3f3f3] disabled:text-[#999]">
                                            <span x-text="selectedModelName() || 'Select model'" class="truncate"></span>
                                            <span>⌄</span>
                                        </button>
                                        <div x-show="modelOpen && selectedBrand" @click.outside="modelOpen = false" x-cloak class="absolute z-20 mt-1 w-full bg-white border border-[#d0d0d0] rounded-lg shadow-lg">
                                            <div class="p-2 border-b border-[#efefef]">
                                                <input x-ref="modelSearchInput" type="text" x-model="modelSearch" placeholder="Search model..."
                                                    class="w-full h-9 border border-[#d0d0d0] rounded px-2 text-[14px]">
                                            </div>
                                            <div class="max-h-56 overflow-y-auto p-1">
                                                @foreach($allModels as $m)
                                                    <button type="button"
                                                        x-show="selectedBrand == '{{ $m->parent_id }}' && (!modelSearch || '{{ strtolower($m->name) }}'.includes(modelSearch.toLowerCase()))"
                                                        @click="selectModel('{{ $m->id }}'); modelOpen = false"
                                                        class="w-full text-left px-2 py-1.5 rounded text-[14px] hover:bg-[#f3f3f3]">
                                                        {{ $m->name }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[16px] font-semibold text-[#111] mb-2">Reference Number</label>
                                <input type="text" name="reference_number" value="{{ old('reference_number') }}" placeholder="e.g., 126610LN"
                                    class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Year *</label>
                                    <select name="year_id" required class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                        <option value="">Select year</option>
                                        @foreach($years as $opt)
                                            <option value="{{ $opt->id }}" {{ old('year_id') == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Condition *</label>
                                    <select name="condition_id" required class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                        <option value="">Select condition</option>
                                        @foreach($conditions as $opt)
                                            <option value="{{ $opt->id }}" {{ old('condition_id') == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
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
                                            <option value="{{ $opt->id }}" {{ old('box_id') == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            <div>
                                <label class="block text-[16px] font-semibold text-[#111] mb-2">Papers *</label>
                                <select name="paper_id" required class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                    <option value="">Select paper option</option>
                                    @foreach($papers as $opt)
                                        <option value="{{ $opt->id }}" {{ old('paper_id') == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="step === 2" x-cloak>
                        <h2 class="text-[28px] font-semibold text-[#111]">Specifications</h2>
                        <p class="text-[16px] text-[#666] mt-2">Technical details help buyers find your watch</p>

                        <div class="mt-6 border border-[#d8d8d8] rounded-xl p-5 space-y-5 bg-[#fafafa]">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Movement</label>
                                    <select name="movement_id" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                        <option value="">Select type</option>
                                        @foreach($movements as $opt)
                                            <option value="{{ $opt->id }}" {{ old('movement_id') == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Case Size (mm)</label>
                                    <input type="text" name="case_size_mm" value="{{ old('case_size_mm') }}" placeholder="e.g., 41" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[16px] font-semibold text-[#111] mb-2">Case Material</label>
                                <select name="case_material_id" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                    <option value="">Select material</option>
                                    @foreach($caseMaterials as $opt)
                                        <option value="{{ $opt->id }}" {{ old('case_material_id') == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[16px] font-semibold text-[#111] mb-2">Dial Colour</label>
                                <select name="dial_colour_id" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                    <option value="">Select dial colour</option>
                                    @foreach($dialColours as $opt)
                                        <option value="{{ $opt->id }}" {{ old('dial_colour_id') == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[16px] font-semibold text-[#111] mb-2">Bracelet Material</label>
                                <select name="bracelet_material_id" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                    <option value="">Select bracelet material</option>
                                    @foreach($braceletMaterials as $opt)
                                        <option value="{{ $opt->id }}" {{ old('bracelet_material_id') == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[16px] font-semibold text-[#111] mb-2">Service History</label>
                                <textarea name="service_history" rows="4" placeholder="Describe any service history..." class="w-full border border-[#d0d0d0] rounded-lg px-3 py-2 text-[16px]">{{ old('service_history') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-[16px] font-semibold text-[#111] mb-2">Description *</label>
                                <textarea name="description" rows="5" required placeholder="Add any additional details about the watch..." class="w-full border border-[#d0d0d0] rounded-lg px-3 py-2 text-[16px]">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div x-show="step === 3" x-cloak>
                        <h2 class="text-[28px] font-semibold text-[#111]">Photos</h2>
                        <p class="text-[16px] text-[#666] mt-2">Upload at least 5 high-quality images</p>

                        <div class="mt-6 border border-[#d8d8d8] rounded-xl p-5 space-y-5 bg-[#fafafa]">
                            <div class="rounded-lg border border-[#e9cf8d] bg-[#fbf6e7] p-4 text-[12px] text-[#7a4e00]">
                                <strong>Required photo angles:</strong>
                                <ul class="list-disc list-inside mt-2 space-y-1">
                                    <li>Dial (front face)</li>
                                    <li>Caseback</li>
                                    <li>Clasp/buckle</li>
                                    <li>Side profile</li>
                                    <li>Wrist shot or size reference</li>
                                </ul>
                            </div>

                            <div>
                                <label class="inline-flex items-center justify-center w-44 h-32 border border-dashed border-[#cfcfcf] rounded-lg bg-white cursor-pointer text-[#666] hover:border-[#bdbdbd]">
                                    <span class="text-center text-[14px]">Add Photos</span>
                                    <input id="photos-input" type="file" name="photos[]" accept=".jpg,.jpeg,.png,.webp" multiple class="hidden" @change="handlePhotos($event)">
                                </label>
                                <p class="text-[13px] text-[#777] mt-2">Minimum 5, Maximum 20 images. First image becomes main image.</p>
                            </div>

                            <template x-for="path in uploadedPhotoPaths" :key="path">
                                <input type="hidden" name="uploaded_photos[]" :value="path">
                            </template>

                            <template x-if="photoPreviews.length > 0">
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                                    <template x-for="(photo, idx) in photoPreviews" :key="photo.id">
                                        <div class="relative rounded-lg overflow-hidden bg-white border border-[#dcdcdc] h-24">
                                            <img :src="photo.url" class="w-full h-full object-cover" alt="preview">
                                            <button type="button"
                                                @click="removePhoto(idx)"
                                                class="absolute top-1 right-1 w-5 h-5 rounded-full bg-white/95 text-[#444] text-[12px] leading-none border border-[#ddd] hover:bg-[#f7f7f7]">
                                                &times;
                                            </button>
                                            <span x-show="idx === 0" class="absolute left-1 bottom-1 text-[11px] px-2 py-0.5 rounded bg-[#1f2937] text-white">Main</span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <p class="text-[13px] text-[#777]" x-show="isUploading">Uploading photos...</p>
                            <p class="text-[14px] text-red-600" x-show="photoError" x-text="photoError"></p>
                        </div>
                    </div>

                    <div x-show="step === 4" x-cloak>
                        <h2 class="text-[28px] font-semibold text-[#111]">Pricing &amp; Location</h2>
                        <p class="text-[16px] text-[#666] mt-2">Set your price and meeting preferences</p>

                        <div class="mt-6 border border-[#d8d8d8] rounded-xl p-5 space-y-5 bg-[#fafafa]">
                            <div>
                                <label class="block text-[16px] font-semibold text-[#111] mb-2">Asking Price (£) *</label>
                                <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required placeholder="e.g., 8500" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                            </div>

                            <div class="space-y-3 border-b border-[#e2e2e2] pb-5">
                                <label class="flex items-center justify-between gap-4">
                                    <span>
                                        <span class="block text-[16px] font-semibold text-[#111]">Price Negotiable</span>
                                        <span class="block text-[14px] text-[#666]">Let buyers know you're open to offers</span>
                                    </span>
                                    <span class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="price_negotiable" value="1" {{ old('price_negotiable') ? 'checked' : '' }} class="sr-only peer">
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
                                        <input type="checkbox" name="accept_traders" value="1" {{ old('accept_traders') ? 'checked' : '' }} class="sr-only peer">
                                        <span class="w-11 h-6 bg-[#e5e7eb] rounded-full transition peer-checked:bg-[#111]"></span>
                                        <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white border border-[#d1d5db] transition peer-checked:translate-x-5"></span>
                                    </span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">City *</label>
                                    <input type="text" name="city" value="{{ old('city', auth()->user()->city) }}" required placeholder="e.g., London" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                </div>
                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Postcode</label>
                                    <input type="text" name="postcode" value="{{ old('postcode', auth()->user()->postal_code) }}" placeholder="e.g., SW1" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[16px] font-semibold text-[#111] mb-2">Meeting Preference</label>
                                <select name="meeting_preference_id" class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                    <option value="">Select preference</option>
                                    @foreach($meetingPreferences as $opt)
                                        <option value="{{ $opt->id }}" {{ old('meeting_preference_id') == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="inline-flex items-center gap-3 text-[16px] text-[#444]">
                                    <input type="checkbox" name="show_phone" value="1" {{ old('show_phone', '1') == '1' ? 'checked' : '' }} class="w-5 h-5 rounded border-[#bdbdbd] text-black focus:ring-black">
                                    <span>Show my phone number on this advert</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-between">
                        <button type="button" @click="prevStep" class="h-11 px-6 rounded-lg border border-[#d1d1d1] bg-white text-[16px] text-[#333]" :class="step === 1 ? 'opacity-50 cursor-not-allowed' : ''" :disabled="step === 1">Back</button>

                        <button type="button" x-show="step < 4" @click="nextStep" class="h-11 px-6 rounded-lg bg-[#171717] text-white text-[16px] font-semibold">Continue</button>
                        <button type="submit" x-show="step === 4" class="h-11 px-6 rounded-lg bg-[#d4b160] text-[#111] text-[16px] font-semibold">Create Listing</button>
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
        function createAdvertWizard() {
            return {
                step: 1,
                selectedBrand: '{{ old('brand_id', '') }}',
                selectedModel: '{{ old('model_id', '') }}',
                brandOpen: false,
                modelOpen: false,
                brandSearch: '',
                modelSearch: '',
                brands: @json($brands->map(fn($b) => ['id' => (string) $b->id, 'name' => $b->name])->values()),
                models: @json($allModels->map(fn($m) => ['id' => (string) $m->id, 'parent_id' => (string) $m->parent_id, 'name' => $m->name])->values()),
                uploadedPhotoPaths: @json($oldUploadedPhotos),
                photoPreviews: @json($photoPreviewSeed),
                photoCount: 0,
                isUploading: false,
                photoError: '',
                steps: [
                    { title: 'Watch Details', subtitle: 'Basic information' },
                    { title: 'Specifications', subtitle: 'Technical details' },
                    { title: 'Photos', subtitle: 'Minimum 5 images' },
                    { title: 'Pricing & Location', subtitle: 'Final step' },
                ],
                init() {
                    this.$watch('step', (value) => {
                        if (value === 3) {
                            this.syncPhotosInput();
                        }
                    });
                    this.photoCount = this.uploadedPhotoPaths.length;
                    if (!this.selectedBrand) {
                        this.selectedModel = '';
                    }
                    this.updatePhotoValidation();
                },
                onBrandChange() {
                    this.selectedModel = '';
                },
                selectBrand(brandId) {
                    this.selectedBrand = String(brandId || '');
                    this.selectedModel = '';
                    this.modelSearch = '';
                },
                selectModel(modelId) {
                    this.selectedModel = String(modelId || '');
                },
                selectedBrandName() {
                    const item = this.brands.find((brand) => brand.id === String(this.selectedBrand));
                    return item ? item.name : '';
                },
                selectedModelName() {
                    const item = this.models.find((model) => model.id === String(this.selectedModel));
                    return item ? item.name : '';
                },
                nextStep() {
                    if (!this.validateStep(this.step)) return;
                    if (this.step < 4) this.step++;
                },
                prevStep() {
                    if (this.step > 1) this.step--;
                },
                validateStep(current) {
                    this.photoError = '';
                    const form = document.getElementById('create-advert-form');
                    if (!form) return true;

                    if (current === 1) {
                        const required = ['title', 'brand_id', 'model_id', 'year_id', 'condition_id', 'box_id', 'paper_id'];
                        for (const name of required) {
                            if (name === 'brand_id' && !String(this.selectedBrand || '').trim()) {
                                this.brandOpen = true;
                                return false;
                            }
                            if (name === 'model_id' && !String(this.selectedModel || '').trim()) {
                                this.modelOpen = true;
                                return false;
                            }
                            const el = form.querySelector(`[name="${name}"]`);
                            if (el && el.type !== 'hidden' && !String(el.value || '').trim()) {
                                el.focus?.();
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

                    if (current === 3) {
                        if (this.isUploading) {
                            this.photoError = 'Please wait, photos are still uploading.';
                            return false;
                        }
                        if (this.photoCount < 5) {
                            this.photoError = 'Please upload at least 5 images.';
                            return false;
                        }
                        if (this.photoCount > 20) {
                            this.photoError = 'You can upload a maximum of 20 images.';
                            return false;
                        }
                    }

                    return true;
                },
                handlePhotos(event) {
                    const input = event.target;
                    const incoming = Array.from(input.files || []);
                    if (!incoming.length) return;

                    this.photoError = '';
                    const availableSlots = 20 - this.uploadedPhotoPaths.length;
                    const files = incoming.slice(0, Math.max(availableSlots, 0));
                    if (!files.length) {
                        this.photoError = 'You can upload a maximum of 20 images.';
                        input.value = '';
                        return;
                    }
                    this.uploadSelectedFiles(files, input);
                },
                async uploadSelectedFiles(files, inputEl) {
                    this.isUploading = true;
                    try {
                        for (const file of files) {
                            const payload = await this.uploadPhoto(file);
                            if (!payload?.path) {
                                continue;
                            }
                            if (this.uploadedPhotoPaths.includes(payload.path)) {
                                continue;
                            }
                            this.uploadedPhotoPaths.push(payload.path);
                            this.photoPreviews.push({
                                id: payload.path,
                                path: payload.path,
                                url: payload.url,
                            });
                        }
                    } catch (error) {
                        this.photoError = 'Image upload failed. Please try again.';
                    } finally {
                        this.isUploading = false;
                        if (inputEl) inputEl.value = '';
                        this.photoCount = this.uploadedPhotoPaths.length;
                        this.updatePhotoValidation();
                    }
                },
                async uploadPhoto(file) {
                    const formData = new FormData();
                    formData.append('photo', file);
                    const response = await fetch('{{ route('adverts.draft-photos.upload') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken(),
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });
                    if (!response.ok) {
                        throw new Error('Upload failed');
                    }
                    return response.json();
                },
                async removePhoto(index) {
                    if (index < 0 || index >= this.photoPreviews.length) return;
                    const removed = this.photoPreviews[index];
                    this.photoPreviews.splice(index, 1);
                    this.uploadedPhotoPaths = this.uploadedPhotoPaths.filter((path) => path !== removed.path);
                    this.photoCount = this.uploadedPhotoPaths.length;
                    this.updatePhotoValidation();

                    if (removed?.path) {
                        try {
                            await fetch('{{ route('adverts.draft-photos.delete') }}', {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': this.csrfToken(),
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ path: removed.path }),
                            });
                        } catch (error) {
                            // Keep UI responsive even if cleanup fails.
                        }
                    }
                },
                updatePhotoValidation() {
                    if (this.photoCount < 5) {
                        this.photoError = 'Please upload at least 5 images.';
                    } else if (this.photoCount > 20) {
                        this.photoError = 'You can upload a maximum of 20 images.';
                    } else {
                        this.photoError = '';
                    }
                },
                syncPhotosInput() {
                    const input = document.getElementById('photos-input');
                    if (!input) return;
                    input.value = '';
                },
                csrfToken() {
                    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                }
            }
        }
    </script>
</x-main-layout>
