<x-main-layout>
    @php
        $productDetailsLocked = $productDetailsLocked ?? false;
        $privatePriceRange = $privatePriceRange ?? null;

        $isDraft = $advert->status === \App\Models\Advert::STATUS_DRAFT;

        // Restore staged photo changes after a validation error. Only accept
        // files that are in this user's drafts folder and still exist.
        $draftPrefix = 'images/adverts/drafts/' . auth()->id() . '/';
        $stagedPhoto = function ($path) use ($draftPrefix) {
            $path = is_string($path) ? trim($path) : '';
            if ($path === '' || !str_starts_with($path, $draftPrefix) || str_contains($path, '..') || !is_file(public_path($path))) {
                return null;
            }
            return ['path' => $path, 'url' => asset($path)];
        };

        $photoState = [
            'existingMain'      => $advert->main_image ? ['url' => $advert->mainImageUrl()] : null,
            'existingGallery'   => $advert->images->map(fn ($img) => ['id' => (string) $img->id, 'url' => $img->url()])->values(),
            'newMain'           => $stagedPhoto(old('main_photo_upload')),
            'newGallery'        => collect((array) old('uploaded_photos', []))->map($stagedPhoto)->filter()->values(),
            'mainRemoved'       => in_array('main', (array) old('remove_photos', []), true),
            'removedGalleryIds' => collect((array) old('remove_photos', []))->reject(fn ($id) => $id === 'main')->map(fn ($id) => (string) $id)->values(),
        ];
    @endphp

    <section x-data="editAdvertWizard()">
        <div class="bg-white border border-[#e1e1e1] rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-[#e5e5e5]">
                <div class="m-auto px-5 lg:px-8 max-w-4xl">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <template x-for="(item, idx) in steps" :key="idx">
                            <div class="flex items-center gap-3 cursor-pointer" @click="step = idx + 1">
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
                <x-flash-messages class="mt-6" />
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
                    <form id="edit-advert-form" action="{{ route('adverts.update', $advert) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8" novalidate @submit.prevent="submitForm">
                        @csrf
                        @method('PUT')

                        {{-- ── Step 1: Watch Details ───────────────────────────────── --}}
                        <div x-show="step === 1" x-cloak x-data="{ selectedBrand: '{{ old('brand_id', $advert->brand_id) }}' }">
                            <h2 class="text-[28px] font-semibold text-[#111]">Watch Details</h2>
                            <p class="text-[16px] text-[#666] mt-2">Update your watch details</p>

                            @if($productDetailsLocked)
                                <p class="mt-3 rounded-lg border border-[#e9cf8d] bg-[#fbf6e7] px-3 py-2 text-[13px] text-[#7a4e00]">Watch identity and specifications are locked after publication. You can still update the title, photos, condition, box and papers, service history, description, price, and location.</p>
                            @endif

                            <div class="mt-6 border border-[#d8d8d8] rounded-xl p-5 space-y-5 bg-[#fafafa]">
                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Title *</label>
                                    <input type="text" name="title" value="{{ old('title', $advert->title) }}" maxlength="255" required
                                        class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">Brand *</label>
                                        <select name="brand_id" @change="selectedBrand = $event.target.value" required {{ $productDetailsLocked ? 'disabled' : '' }}
                                            class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px] {{ $productDetailsLocked ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                            <option value="">Select brand</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id', $advert->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($productDetailsLocked)
                                            <input type="hidden" name="brand_id" value="{{ old('brand_id', $advert->brand_id) }}">
                                        @endif
                                    </div>

                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">Model *</label>
                                        <select name="model_id" required {{ $productDetailsLocked ? 'disabled' : '' }}
                                            class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px] {{ $productDetailsLocked ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                            <option value="">Select model</option>
                                            @foreach($allModels as $m)
                                                <option value="{{ $m->id }}" data-parent="{{ $m->parent_id }}" x-show="!selectedBrand || selectedBrand == '{{ $m->parent_id }}'" {{ old('model_id', $advert->model_id) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($productDetailsLocked)
                                            <input type="hidden" name="model_id" value="{{ old('model_id', $advert->model_id) }}">
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Reference Number</label>
                                    <input type="text" name="reference_number" value="{{ old('reference_number', $advert->reference_number) }}" placeholder="e.g., 126610LN" {{ $productDetailsLocked ? 'readonly' : '' }}
                                        class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px] {{ $productDetailsLocked ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">Year *</label>
                                        <select name="year_id" required {{ $productDetailsLocked ? 'disabled' : '' }} class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px] {{ $productDetailsLocked ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                            <option value="">Select year</option>
                                            @foreach($years->reverse() as $opt)
                                                <option value="{{ $opt->id }}" {{ old('year_id', $advert->year_id) == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($productDetailsLocked)
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

                        {{-- ── Step 2: Specifications ──────────────────────────────── --}}
                        <div x-show="step === 2" x-cloak>
                            <h2 class="text-[28px] font-semibold text-[#111]">Specifications</h2>
                            <p class="text-[16px] text-[#666] mt-2">Update technical details</p>

                            <div class="mt-6 border border-[#d8d8d8] rounded-xl p-5 space-y-5 bg-[#fafafa]">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">Movement</label>
                                        <select name="movement_id" {{ $productDetailsLocked ? 'disabled' : '' }} class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px] {{ $productDetailsLocked ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                            <option value="">Select type</option>
                                            @foreach($movements as $opt)
                                                <option value="{{ $opt->id }}" {{ old('movement_id', $advert->movement_id) == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[16px] font-semibold text-[#111] mb-2">Case Size (mm)</label>
                                        <input type="text" name="case_size_mm" value="{{ old('case_size_mm', $advert->case_size_mm) }}" placeholder="e.g., 41" {{ $productDetailsLocked ? 'readonly' : '' }} class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px] {{ $productDetailsLocked ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Case Material</label>
                                    <select name="case_material_id" {{ $productDetailsLocked ? 'disabled' : '' }} class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px] {{ $productDetailsLocked ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                        <option value="">Select material</option>
                                        @foreach($caseMaterials as $opt)
                                            <option value="{{ $opt->id }}" {{ old('case_material_id', $advert->case_material_id) == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Dial Colour</label>
                                    <select name="dial_colour_id" {{ $productDetailsLocked ? 'disabled' : '' }} class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px] {{ $productDetailsLocked ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
                                        <option value="">Select dial colour</option>
                                        @foreach($dialColours as $opt)
                                            <option value="{{ $opt->id }}" {{ old('dial_colour_id', $advert->dial_colour_id) == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Bracelet Material</label>
                                    <select name="bracelet_material_id" {{ $productDetailsLocked ? 'disabled' : '' }} class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px] {{ $productDetailsLocked ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}">
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

                        {{-- ── Step 3: Photos ──────────────────────────────────────── --}}
                        <div x-show="step === 3" x-cloak>
                            <h2 class="text-[28px] font-semibold text-[#111]">Photos</h2>
                            <p class="text-[15px] text-[#666] mt-1">Your main photo is the first thing buyers see. Replace it or add more to the gallery.</p>

                            {{-- Staged changes: applied only when the form is saved --}}
                            <input type="hidden" name="main_photo_upload" :value="newMain ? newMain.path : ''">
                            <template x-if="mainRemoved && existingMain">
                                <input type="hidden" name="remove_photos[]" value="main">
                            </template>
                            <template x-for="id in removedGalleryIds" :key="'rm-' + id">
                                <input type="hidden" name="remove_photos[]" :value="id">
                            </template>
                            <template x-for="photo in newGallery" :key="'up-' + photo.path">
                                <input type="hidden" name="uploaded_photos[]" :value="photo.path">
                            </template>
                            <input x-ref="mainInput" type="file" accept=".jpg,.jpeg,.png,.webp" class="hidden" @change="onMainFiles($event.target.files); $event.target.value = null">
                            <input x-ref="galleryInput" type="file" accept=".jpg,.jpeg,.png,.webp" multiple class="hidden" @change="addGalleryFiles($event.target.files); $event.target.value = null">

                            {{-- Main photo --}}
                            <div class="mt-6 bg-white border rounded-xl p-4 md:p-5 transition-colors"
                                 :class="mainChanged ? 'border-[#111]' : 'border-[#e3e3e3]'">
                                <div class="flex flex-col md:flex-row gap-3 md:gap-6">
                                    <div class="md:hidden flex items-center justify-between gap-3">
                                        <h3 class="text-[16px] font-semibold text-[#111]">Main photo</h3>
                                        <span class="text-[11px] font-semibold rounded-full px-2 py-0.5"
                                              :class="mainChanged ? 'bg-[#fdf1d8] text-[#8a5a00]' : 'bg-[#f1f1f1] text-[#555]'"
                                              x-text="mainChanged ? 'Not saved yet' : 'Shown first'"></span>
                                    </div>

                                    {{-- Preview (also a drop target on desktop) --}}
                                    <div class="relative md:w-[300px] md:shrink-0 aspect-[4/3] rounded-lg overflow-hidden bg-[#f3f3f3] border-2 transition"
                                         :class="dragTarget === 'main' ? 'border-dashed border-[#111]' : (newMain ? 'border-[#111]' : 'border-transparent')"
                                         @dragover.prevent="dragTarget = 'main'" @dragleave.prevent="dragTarget = null"
                                         @drop.prevent="dragTarget = null; onMainFiles($event.dataTransfer.files)">
                                        <template x-if="mainPreviewUrl">
                                            <img :src="mainPreviewUrl" alt="Main photo" class="w-full h-full object-cover transition" :class="mainRemoved && !newMain && !promotedFromGallery ? 'opacity-40 grayscale' : ''">
                                        </template>
                                        <template x-if="!mainPreviewUrl">
                                            <button type="button" @click="$refs.mainInput.click()" class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-[#666] hover:text-[#111]">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                                                <span class="text-[14px] font-medium">Add main photo</span>
                                            </button>
                                        </template>
                                        <span x-show="newMain" class="absolute top-2 left-2 inline-flex items-center gap-1 rounded-full bg-[#111] text-white text-[11px] font-semibold px-2 py-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            New photo
                                        </span>
                                        <span x-show="promotedFromGallery" class="absolute top-2 left-2 rounded-full bg-white/95 text-[#111] text-[11px] font-semibold px-2 py-1 border border-[#ddd]">From gallery</span>
                                        <div x-show="mainUploading" class="absolute inset-0 bg-white/70 flex items-center justify-center text-[13px] font-medium text-[#333]">Uploading…</div>
                                    </div>

                                    {{-- Details & actions --}}
                                    <div class="flex-1 min-w-0 flex flex-col gap-3">
                                        <div class="hidden md:flex items-center gap-3">
                                            <h3 class="text-[16px] font-semibold text-[#111]">Main photo</h3>
                                            <span class="text-[11px] font-semibold rounded-full px-2 py-0.5"
                                                  :class="mainChanged ? 'bg-[#fdf1d8] text-[#8a5a00]' : 'bg-[#f1f1f1] text-[#555]'"
                                                  x-text="mainChanged ? 'Not saved yet' : 'Shown first'"></span>
                                        </div>
                                        <p class="hidden md:block text-[13px] text-[#666]" x-show="!mainChanged">Appears at the top of your listing and in search results. Landscape photos in good light work best.</p>

                                        {{-- Pending-change strips --}}
                                        <div x-show="newMain && existingMain" x-cloak class="flex items-center gap-3 rounded-lg bg-[#f6f6f6] border border-[#ececec] p-2.5">
                                            <img :src="existingMain ? existingMain.url : ''" alt="" class="w-12 h-10 rounded object-cover shrink-0">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[13px] font-semibold text-[#111]">Replacing current photo</p>
                                                <p class="text-[12px] text-[#777]">Your listing updates when you save</p>
                                            </div>
                                            <button type="button" @click="undoMain()" class="text-[13px] font-semibold text-[#111] underline underline-offset-2 px-2 py-2">Undo</button>
                                        </div>
                                        <div x-show="mainRemoved && existingMain && !newMain" x-cloak class="flex items-center gap-3 rounded-lg bg-[#fff4f4] border border-[#f5d0d0] p-2.5">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[13px] font-semibold text-[#9b1c1c]">Current main photo will be removed</p>
                                                <p class="text-[12px] text-[#9b4b4b]" x-text="visibleGallery.length ? 'Your first gallery photo becomes the main photo when you save.' : 'Choose a new main photo before saving.'"></p>
                                            </div>
                                            <button type="button" @click="undoMain()" class="text-[13px] font-semibold text-[#111] underline underline-offset-2 px-2 py-2">Undo</button>
                                        </div>

                                        <div class="grid grid-cols-2 md:flex md:flex-wrap gap-3">
                                            <button type="button" @click="$refs.mainInput.click()" :disabled="mainUploading"
                                                    class="inline-flex items-center justify-center gap-2 h-11 px-4 rounded-lg border border-[#111] bg-white text-[14px] font-semibold text-[#111] hover:bg-[#f5f5f5] disabled:opacity-50">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.66-.9l.82-1.2A2 2 0 0110.07 4h3.86a2 2 0 011.66.9l.82 1.2a2 2 0 001.66.9H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                                                <span x-text="newMain ? 'Choose another' : (existingMain && !mainRemoved ? 'Replace photo' : 'Choose photo')"></span>
                                            </button>
                                            <button type="button" @click="removeMain()" x-show="(existingMain && !mainRemoved) || newMain"
                                                    class="inline-flex items-center justify-center gap-2 h-11 px-4 rounded-lg border border-[#e5e5e5] bg-white text-[14px] font-semibold text-[#c62828] hover:bg-[#fff5f5]">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6M14 11v6M5 7l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12M9 7V4h6v3"/></svg>
                                                Remove photo
                                            </button>
                                        </div>
                                        <p class="text-[12px] text-[#777]">JPG, PNG or WEBP, up to 5MB. Landscape works best.<span class="hidden md:inline"> You can also drag a photo onto the image.</span></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Gallery --}}
                            <div class="mt-5 bg-white border border-[#e3e3e3] rounded-xl p-4 md:p-5">
                                <div class="flex items-center justify-between md:justify-start gap-3">
                                    <h3 class="text-[16px] font-semibold text-[#111]">Gallery</h3>
                                    <span class="text-[12px] text-[#777]" x-text="visibleGallery.length + (visibleGallery.length === 1 ? ' photo' : ' photos')"></span>
                                </div>

                                <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                    <template x-for="(photo, idx) in visibleGallery" :key="photo.key">
                                        <div class="relative aspect-square rounded-lg overflow-hidden bg-[#f3f3f3] border"
                                             :class="photo.isNew ? 'border-[#111]' : 'border-[#e3e3e3]'">
                                            <img :src="photo.url" alt="Gallery photo" class="w-full h-full object-cover" :class="idx === 0 && promotedFromGallery ? 'opacity-50' : ''">
                                            <button type="button" @click="removeGallery(photo)" aria-label="Remove photo"
                                                    class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-[#111]/80 text-white flex items-center justify-center hover:bg-[#111]">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
                                            </button>
                                            <span x-show="idx === 0 && promotedFromGallery" class="absolute left-1.5 bottom-1.5 text-[10px] font-semibold px-1.5 py-0.5 rounded bg-white text-[#111]">Becomes main</span>
                                            <span x-show="photo.isNew && !(idx === 0 && promotedFromGallery)" class="absolute left-1.5 bottom-1.5 text-[10px] font-semibold px-1.5 py-0.5 rounded bg-[#111] text-white">New</span>
                                        </div>
                                    </template>
                                    <template x-for="n in galleryUploading" :key="'loading-' + n">
                                        <div class="aspect-square rounded-lg bg-[#f3f3f3] border border-[#e3e3e3] flex items-center justify-center text-[11px] text-[#777]">Uploading…</div>
                                    </template>
                                    {{-- Mobile add tile --}}
                                    <button type="button" @click="$refs.galleryInput.click()"
                                            class="md:hidden aspect-square rounded-lg border border-dashed border-[#bdbdbd] bg-[#fafafa] flex flex-col items-center justify-center gap-1 text-[#333] hover:border-[#111]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                                        <span class="text-[12px] font-medium">Add photos</span>
                                    </button>
                                </div>

                                {{-- Desktop drop zone --}}
                                <button type="button" @click="$refs.galleryInput.click()"
                                        @dragover.prevent="dragTarget = 'gallery'" @dragleave.prevent="dragTarget = null"
                                        @drop.prevent="dragTarget = null; addGalleryFiles($event.dataTransfer.files)"
                                        class="hidden md:flex w-full mt-4 flex-col items-center justify-center gap-1 rounded-lg border-2 border-dashed py-7 transition"
                                        :class="dragTarget === 'gallery' ? 'border-[#111] bg-[#f5f5f5]' : 'border-[#d6d6d6] bg-[#fafafa] hover:border-[#999]'">
                                    <svg class="w-5 h-5 text-[#333]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0l-4 4m4-4l4 4M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
                                    <span class="text-[14px] text-[#111]"><strong>Add gallery photos</strong> — drag files here or click to browse</span>
                                    <span class="text-[12px] text-[#777]">Dial, case back, clasp, box &amp; papers all help buyers.</span>
                                </button>
                                <p class="md:hidden mt-3 text-[12px] text-[#777]">Add more angles: dial, case back, clasp, box &amp; papers.</p>
                            </div>

                            <p class="mt-4 text-[14px] text-red-600" x-show="photoError" x-text="photoError"></p>
                        </div>

                        {{-- ── Step 4: Pricing & Location ──────────────────────────── --}}
                        <div x-show="step === 4" x-cloak>
                            <h2 class="text-[28px] font-semibold text-[#111]">Pricing &amp; Location</h2>
                            <p class="text-[16px] text-[#666] mt-2">Update price and preferences</p>

                            <div class="mt-6 border border-[#d8d8d8] rounded-xl p-5 space-y-5 bg-[#fafafa]">
                                <div>
                                    <label class="block text-[16px] font-semibold text-[#111] mb-2">Asking Price (&pound;) *</label>
                                    <input type="number" name="price" id="price-input" value="{{ old('price', $advert->price) }}" step="1" min="{{ $privatePriceRange['min'] ?? 0 }}" @if($privatePriceRange) max="{{ $privatePriceRange['max'] }}" @else max="999999" @endif required class="w-full h-10 border border-[#d0d0d0] rounded-lg px-3 text-[14px]">
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
                                            <span class="block text-[16px] font-semibold text-[#111]">Accept Part-Exchange</span>
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

                        {{-- ── Navigation buttons ──────────────────────────────────── --}}
                        <div class="mt-8 flex flex-wrap items-center gap-3 justify-between">
                            <p x-show="hasUnsavedPhotoChanges" x-cloak class="w-full flex items-center gap-2 text-[13px] text-[#8a5a00]">
                                <span class="w-2 h-2 rounded-full bg-[#e0a100]"></span>
                                You have unsaved photo changes
                            </p>
                            <button type="button" @click="prevStep"
                                    class="h-11 px-6 rounded-lg border border-[#d1d1d1] bg-white text-[16px] text-[#333]"
                                    :class="step === 1 ? 'opacity-50 cursor-not-allowed' : ''"
                                    :disabled="step === 1">
                                Back
                            </button>

                            @if($advert->status === \App\Models\Advert::STATUS_DRAFT)
                            <button type="button" @click="saveDraft()"
                                    class="h-11 px-5 rounded-lg border border-[#d1d1d1] bg-white text-[15px] text-[#555] hover:bg-[#f5f5f5]">
                                Save as Draft
                            </button>
                            @endif

                            <button type="button" x-show="step < 4" @click="nextStep"
                                    class="h-11 px-6 rounded-lg bg-[#171717] text-white text-[16px] font-semibold">
                                Continue
                            </button>

                            @if($advert->status === \App\Models\Advert::STATUS_DRAFT)
                                <button type="button" x-show="step === 4" @click="publishDraft()"
                                        class="h-11 px-6 rounded-lg bg-[#d4b160] text-[#111] text-[16px] font-semibold">
                                    Publish Listing
                                </button>
                            @else
                                <button type="button" x-show="step === 4" @click="submitForm"
                                        class="h-11 px-6 rounded-lg bg-[#d4b160] text-[#111] text-[16px] font-semibold">
                                    Update Listing
                                </button>
                            @endif
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

                // Photo state. New photos are uploaded to the drafts folder
                // straight away but only applied to the advert on save.
                isDraft: @json($isDraft),
                existingMain: @json($photoState['existingMain']),
                existingGallery: @json($photoState['existingGallery']),
                newMain: @json($photoState['newMain']),
                newGallery: @json($photoState['newGallery']),
                mainRemoved: @json($photoState['mainRemoved']),
                removedGalleryIds: @json($photoState['removedGalleryIds']),
                mainUploading: false,
                galleryUploading: 0,
                dragTarget: null,
                photoError: '',

                get isUploading() {
                    return this.mainUploading || this.galleryUploading > 0;
                },
                get visibleGallery() {
                    const kept = this.existingGallery
                        .filter(p => !this.removedGalleryIds.includes(p.id))
                        .map(p => ({ ...p, key: 'e-' + p.id, isNew: false }));
                    const added = this.newGallery.map(p => ({ ...p, key: 'n-' + p.path, isNew: true }));
                    return [...kept, ...added];
                },
                get hasMainPhoto() {
                    return !!this.newMain || (!!this.existingMain && !this.mainRemoved);
                },
                // No main photo staged: the first gallery photo is promoted on save.
                get promotedFromGallery() {
                    return !this.hasMainPhoto && this.visibleGallery.length > 0;
                },
                get mainPreviewUrl() {
                    if (this.newMain) return this.newMain.url;
                    if (this.existingMain && !this.mainRemoved) return this.existingMain.url;
                    if (this.promotedFromGallery) return this.visibleGallery[0].url;
                    return this.existingMain ? this.existingMain.url : null;
                },
                get mainChanged() {
                    return !!this.newMain || (this.mainRemoved && !!this.existingMain);
                },
                get hasUnsavedPhotoChanges() {
                    return this.mainChanged || this.newGallery.length > 0 || this.removedGalleryIds.length > 0;
                },
                get totalPhotos() {
                    return (this.hasMainPhoto ? 1 : 0) + this.visibleGallery.length;
                },

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

                    if (current === 3) {
                        if (this.isUploading) {
                            this.photoError = 'Please wait, photos are still uploading.';
                            return false;
                        }
                        if (this.isDraft && this.totalPhotos < 2) {
                            this.photoError = 'Please upload at least 2 images.';
                            return false;
                        }
                        if (!this.isDraft && this.totalPhotos < 1) {
                            this.photoError = 'Your listing needs a main photo. Choose one before saving.';
                            return false;
                        }
                        this.photoError = '';
                    }

                    if (current === 4) {
                        const price = form.querySelector('[name="price"]');
                        if (price && !String(price.value || '').trim()) {
                            price.focus();
                            return false;
                        }

                        @if($privatePriceRange)
                            const minPrice = {{ (float) $privatePriceRange['min'] }};
                            const maxPrice = {{ (float) $privatePriceRange['max'] }};
                            const currentPrice = parseFloat(price?.value || '0');
                            if (currentPrice < minPrice || currentPrice > maxPrice) {
                                price?.focus();
                                alert(`You can set pricing only between £${minPrice.toFixed(2)} and £${maxPrice.toFixed(2)} for this package.`);
                                return false;
                            }
                        @else
                            const currentPrice = parseFloat(price?.value || '0');
                            if (currentPrice > 999999) {
                                price?.focus();
                                alert('Price cannot exceed £999,999');
                                return false;
                            }
                        @endif
                    }

                    return true;
                },
                submitForm() {
                    const form = document.getElementById('edit-advert-form');
                    if (!form) return;

                    for (const targetStep of [1, 2, 3, 4]) {
                        if (!this.validateStep(targetStep)) {
                            this.step = targetStep;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                            return;
                        }
                    }

                    HTMLFormElement.prototype.submit.call(form);
                },
                saveDraft() {
                    const form = document.getElementById('edit-advert-form');
                    if (!form) return;
                    if (this.isUploading) {
                        this.step = 3;
                        this.photoError = 'Please wait, photos are still uploading.';
                        return;
                    }
                    const methodInput = form.querySelector('input[name="_method"]');
                    form.action = '{{ route('adverts.updateDraft', $advert) }}';
                    if (methodInput) methodInput.remove();
                    HTMLFormElement.prototype.submit.call(form);
                },
                publishDraft() {
                    const form = document.getElementById('edit-advert-form');
                    if (!form) return;

                    for (const targetStep of [1, 2, 3, 4]) {
                        if (!this.validateStep(targetStep)) {
                            this.step = targetStep;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                            return;
                        }
                    }

                    const methodInput = form.querySelector('input[name="_method"]');
                    form.action = '{{ route('adverts.publishDraft', $advert) }}';
                    if (methodInput) methodInput.remove();
                    HTMLFormElement.prototype.submit.call(form);
                },

                // ── Photo methods ───────────────────────────────────────────────
                validImageFiles(fileList) {
                    const files = Array.from(fileList || []);
                    const allowed = ['image/jpeg', 'image/png', 'image/webp'];
                    for (const file of files) {
                        if (!allowed.includes(file.type)) {
                            this.photoError = `"${file.name}" isn't a supported image. Use JPG, PNG or WEBP.`;
                            return [];
                        }
                        if (file.size > 5 * 1024 * 1024) {
                            this.photoError = `"${file.name}" is too large. Maximum size is 5MB.`;
                            return [];
                        }
                    }
                    this.photoError = '';
                    return files;
                },
                async onMainFiles(fileList) {
                    const [file] = this.validImageFiles(fileList);
                    if (!file) return;

                    this.mainUploading = true;
                    try {
                        const payload = await this.uploadPhotoAjax(file);
                        if (!payload?.path) throw new Error('Upload failed');
                        if (this.newMain) this.deleteDraftPhotoRequest(this.newMain.path);
                        this.newMain = { path: payload.path, url: payload.url };
                        this.mainRemoved = false;
                    } catch {
                        this.photoError = 'Photo upload failed. Please try again.';
                    } finally {
                        this.mainUploading = false;
                    }
                },
                undoMain() {
                    if (this.newMain) this.deleteDraftPhotoRequest(this.newMain.path);
                    this.newMain = null;
                    this.mainRemoved = false;
                    this.photoError = '';
                },
                removeMain() {
                    if (this.newMain) {
                        this.deleteDraftPhotoRequest(this.newMain.path);
                        this.newMain = null;
                    }
                    if (this.existingMain) this.mainRemoved = true;
                },
                async addGalleryFiles(fileList) {
                    const files = this.validImageFiles(fileList);
                    for (const file of files) {
                        this.galleryUploading++;
                        try {
                            const payload = await this.uploadPhotoAjax(file);
                            if (payload?.path && !this.newGallery.some(p => p.path === payload.path)) {
                                this.newGallery.push({ path: payload.path, url: payload.url });
                            }
                        } catch {
                            this.photoError = `"${file.name}" failed to upload. Please try again.`;
                        } finally {
                            this.galleryUploading--;
                        }
                    }
                },
                removeGallery(photo) {
                    if (photo.isNew) {
                        this.newGallery = this.newGallery.filter(p => p.path !== photo.path);
                        this.deleteDraftPhotoRequest(photo.path);
                    } else if (!this.removedGalleryIds.includes(photo.id)) {
                        this.removedGalleryIds.push(photo.id);
                    }
                },
                async uploadPhotoAjax(file) {
                    const formData = new FormData();
                    formData.append('photo', file);
                    const response = await fetch('{{ route('adverts.draft-photos.upload') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrfToken(), 'Accept': 'application/json' },
                        body: formData,
                    });
                    if (!response.ok) throw new Error('Upload failed');
                    return response.json();
                },
                async deleteDraftPhotoRequest(path) {
                    try {
                        await fetch('{{ route('adverts.draft-photos.delete') }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken(),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ path }),
                        });
                    } catch {}
                },
                csrfToken() {
                    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                },
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
            @else
                const priceInput = document.getElementById('price-input');
                const currentPrice = parseFloat(priceInput?.value || '0');
                if (currentPrice > 999999) {
                    alert('Price cannot exceed £999,999');
                    e.preventDefault();
                    return;
                }
            @endif
        });
    </script>
</x-main-layout>
