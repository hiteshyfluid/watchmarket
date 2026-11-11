<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\AdvertImage;
use App\Models\AttributeOption;
use App\Models\Brand;
use App\Models\Category;
use App\Models\MembershipLevel;
use App\Models\MembershipOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AdvertController extends Controller
{
    public function create()
    {
        if ($redirect = $this->ensureTradeAdvertLimitAvailable()) {
            return $redirect;
        }

        $data = $this->formData();
        return view('adverts.create', $data);
    }

    public function storeDraft(Request $request)
    {
        $this->authorize_seller();

        $validated = $request->validate($this->draftRules());
        $validated = $this->normalizeAdvertBooleans($request, $validated);

        $uploadedPhotoPaths = $this->sanitizeDraftPhotoPaths((array) $request->input('uploaded_photos', []));

        $user    = auth()->user();
        $fields  = $this->advertDraftFields($validated);
        $mainImagePath = null;

        if (!empty($uploadedPhotoPaths)) {
            $mainImagePath = $this->moveDraftPhotoToFinal(array_shift($uploadedPhotoPaths), true);
        }

        $advert = $user->adverts()->create(array_merge($fields, [
            'main_image'  => $mainImagePath,
            'status'      => Advert::STATUS_DRAFT,
            'expiry_date' => null,
        ]));

        foreach (array_values($uploadedPhotoPaths) as $i => $path) {
            $finalPath = $this->moveDraftPhotoToFinal($path, false);
            if ($finalPath) {
                $advert->images()->create(['image_path' => $finalPath, 'sort_order' => $i]);
            }
        }

        return redirect()->route('my-account')
            ->with('success', 'Draft saved! You can continue editing it from your Dashboard anytime.');
    }

    public function updateDraft(Request $request, Advert $advert)
    {
        $this->authorize_owner($advert);
        abort_unless($advert->status === Advert::STATUS_DRAFT, 403, 'Only drafts can be saved this way.');

        $validated = $request->validate($this->draftRules());
        $validated = $this->normalizeAdvertBooleans($request, $validated);

        $advert->update($this->advertDraftFields($validated));
        $this->applyPhotoChanges($request, $advert, false);

        return redirect()->route('my-account')
            ->with('success', 'Draft updated! You can continue editing it from your Dashboard anytime.');
    }

    public function publishDraft(Request $request, Advert $advert)
    {
        $this->authorize_owner($advert);
        abort_unless($advert->status === Advert::STATUS_DRAFT, 403, 'Only drafts can be published this way.');

        $validated = $request->validate($this->rules());
        $validated = $this->normalizeAdvertBooleans($request, $validated);

        if ($this->plannedPhotoCount($request, $advert) < 2) {
            throw ValidationException::withMessages(['photos' => 'Please upload at least 2 images.']);
        }

        $this->applyPhotoChanges($request, $advert, true);
        $advert->refresh();

        $advert->update(array_merge($this->advertFields($validated), [
            'main_image'  => $advert->main_image,
            'status'      => Advert::STATUS_DRAFT,
            'expiry_date' => null,
        ]));

        $user = auth()->user();
        if ($user->isPrivateSeller()) {
            return redirect()->route('seller.private.packages', $advert)
                ->with('success', 'Listing complete! Please choose a package to activate it.');
        }

        $advert->update(['status' => Advert::STATUS_ACTIVE, 'expiry_date' => $user->isTradeSeller() ? null : now()->addMonths(3)]);
        return redirect()->route('my-account')->with('success', 'Advert published successfully!');
    }

    public function store(Request $request)
    {
        $this->authorize_seller();
        if ($redirect = $this->ensureTradeAdvertLimitAvailable()) {
            return $redirect;
        }

        $validated = $request->validate($this->rules());
        $validated = $this->normalizeAdvertBooleans($request, $validated);

        $photoFiles = $request->file('photos', []);
        $uploadedPhotoPaths = $this->sanitizeDraftPhotoPaths((array) $request->input('uploaded_photos', []));
        $uploadedPhotoCount = count($uploadedPhotoPaths);
        $photoCount = max(count($photoFiles), $uploadedPhotoCount);

        if ($photoCount < 2) {
            throw ValidationException::withMessages([
                'photos' => 'Please upload at least 2 images.',
            ]);
        }

        $mainImagePath = null;

        if (!empty($photoFiles)) {
            $mainImagePath = $this->storeImageInPublic($photoFiles[0], 'adverts/main');
        } elseif (!empty($uploadedPhotoPaths)) {
            $mainImagePath = $this->moveDraftPhotoToFinal(array_shift($uploadedPhotoPaths), true);
        } elseif ($request->hasFile('main_image')) {
            // Fallback for legacy form submissions.
            $mainImagePath = $this->storeImageInPublic($request->file('main_image'), 'adverts/main');
        }

        $user = auth()->user();
        $isPrivateSeller = $user->isPrivateSeller();
        $isTradeSeller = $user->isTradeSeller();

        $advert = $user->adverts()->create(array_merge(
            $this->advertFields($validated),
            [
                'main_image'  => $mainImagePath,
                'status'      => $isPrivateSeller ? Advert::STATUS_DRAFT : Advert::STATUS_ACTIVE,
                'expiry_date' => ($isPrivateSeller || $isTradeSeller) ? null : now()->addMonths(3),
            ]
        ));

        // Handle gallery images: remaining images from multi-step photo upload.
        if (!empty($photoFiles)) {
            foreach (array_slice($photoFiles, 1) as $index => $file) {
                $path = $this->storeImageInPublic($file, 'adverts/gallery');
                $advert->images()->create(['image_path' => $path, 'sort_order' => $index]);
            }
        } elseif (!empty($uploadedPhotoPaths)) {
            foreach (array_values($uploadedPhotoPaths) as $index => $draftPath) {
                $path = $this->moveDraftPhotoToFinal($draftPath, false);
                if ($path) {
                    $advert->images()->create(['image_path' => $path, 'sort_order' => $index]);
                }
            }
        } elseif ($request->hasFile('gallery')) {
            // Legacy fallback.
            foreach ($request->file('gallery') as $index => $file) {
                $path = $this->storeImageInPublic($file, 'adverts/gallery');
                $advert->images()->create(['image_path' => $path, 'sort_order' => $index]);
            }
        }

        if ($isPrivateSeller) {
            return redirect()->route('seller.private.packages', $advert)
                ->with('success', 'Advert details saved. Please complete package checkout to activate this advert.');
        }

        return redirect()->route('my-account')->with('success', 'Advert created successfully!');
    }

    public function edit(Advert $advert)
    {
        $this->authorize_owner($advert);
        $data = $this->formData();
        $productDetailsLocked = $this->isPublishedAdvert($advert);
        $privatePriceRange = $this->isPrivatePublishedAdvert($advert) ? $this->privatePublishedPriceRange($advert) : null;

        return view('adverts.edit', array_merge($data, compact('advert', 'productDetailsLocked', 'privatePriceRange')));
    }

    public function update(Request $request, Advert $advert)
    {
        $this->authorize_owner($advert);

        $validated = $request->validate($this->rules(false));
        $validated = $this->normalizeAdvertBooleans($request, $validated);
        $this->enforcePublishedAdvertRestrictions($advert, $validated);

        $this->applyPhotoChanges($request, $advert, true);
        $advert->refresh();

        $advert->update($this->advertFields($validated, $advert->main_image, $advert));
        if (auth()->user()->isTradeSeller()) {
            $advert->update(['expiry_date' => null]);
        }

        return redirect()->route('my-account')
            ->with('success', 'Advert updated successfully!');
    }

    public function destroy(Advert $advert)
    {
        $this->authorize_owner($advert);

        // Delete stored images
        if ($advert->main_image) {
            $this->deleteImagePath($advert->main_image);
        }
        foreach ($advert->images as $img) {
            $this->deleteImagePath($img->image_path);
        }

        $advert->delete();

        return redirect()->route('my-account')
            ->with('success', 'Advert deleted.');
    }

    // ----------------------------------------------------------------
    // Delete a single gallery image (called via POST from edit form)
    // ----------------------------------------------------------------
    public function deleteImage(Request $request, Advert $advert, AdvertImage $image)
    {
        $this->authorize_owner($advert);

        $this->deleteImagePath($image->image_path);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    public function togglePause(Advert $advert)
    {
        $this->authorize_owner($advert);

        if ($advert->status === Advert::STATUS_ACTIVE) {
            $advert->update(['status' => Advert::STATUS_PAUSED]);
            return back()->with('success', 'Advert put on hold.');
        }

        if ($advert->status === Advert::STATUS_PAUSED) {
            $advert->update(['status' => Advert::STATUS_ACTIVE]);
            return back()->with('success', 'Advert resumed.');
        }

        return back()->with('error', 'Only active or paused adverts can be toggled.');
    }

    public function markSold(Advert $advert)
    {
        $this->authorize_owner($advert);

        $advert->update([
            'status' => Advert::STATUS_SOLD,
            'is_sold' => true,
        ]);

        return back()->with('success', 'Advert marked as sold.');
    }

    // ----------------------------------------------------------------
    // API endpoint: return models for a given brand (for dynamic select)
    // ----------------------------------------------------------------
    public function modelsByBrand(Brand $brand)
    {
        return response()->json(
            $brand->children()->active()->orderBy('name')->get(['id', 'name'])
        );
    }

    public function uploadDraftPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $userId = (int) auth()->id();
        $path = $this->storeImageInPublic($request->file('photo'), "adverts/drafts/{$userId}");

        return response()->json([
            'ok' => true,
            'path' => $path,
            'url' => asset($path),
        ]);
    }

    public function deleteDraftPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = (string) $request->input('path');
        $userId = (int) auth()->id();
        $prefix = "images/adverts/drafts/{$userId}/";

        if (str_starts_with($path, $prefix)) {
            $this->deleteImagePath($path);
        }

        return response()->json(['ok' => true]);
    }

    // ----------------------------------------------------------------
    // Private helpers
    // ----------------------------------------------------------------

    private function formData(): array
    {
        return [
            'brands'             => Brand::parents()->active()->orderByDesc('is_popular')->orderBy('name')->get(),
            'allModels'          => Brand::query()
                ->from('brands as model')
                ->select('model.*')
                ->join('brands as parent', 'parent.id', '=', 'model.parent_id')
                ->whereNotNull('model.parent_id')
                ->where('model.is_active', true)
                ->where('parent.is_active', true)
                ->orderByDesc('parent.is_popular')
                ->orderByDesc('model.is_popular')
                ->orderBy('model.name')
                ->get(),
            'categories'         => Category::parents()->active()->orderBy('sort_order')->orderBy('name')->get(),
            'papers'             => AttributeOption::ofType('paper')->active()->ordered()->get(),
            'boxes'              => AttributeOption::ofType('box')->active()->ordered()->get(),
            'years'              => AttributeOption::ofType('year')->active()->ordered()->get(),
            'genders'            => AttributeOption::ofType('gender')->active()->ordered()->get(),
            'conditions'         => AttributeOption::ofType('condition')->active()->ordered()->get(),
            'movements'          => AttributeOption::ofType('movement')->active()->ordered()->get(),
            'caseMaterials'      => AttributeOption::ofType('case_material')->active()->ordered()->get(),
            'braceletMaterials'  => AttributeOption::ofType('bracelet_material')->active()->ordered()->get(),
            'dialColours'        => AttributeOption::ofType('dial_colour')->active()->ordered()->get(),
            'caseDiameters'      => AttributeOption::ofType('case_diameter')->active()->ordered()->get(),
            'waterproofs'        => AttributeOption::ofType('waterproof')->active()->ordered()->get(),
            'meetingPreferences' => AttributeOption::ofType('meeting_preference')->active()->ordered()->get(),
        ];
    }

    private function draftRules(): array
    {
        return [
            'title'                => 'nullable|string|max:255',
            'description'          => 'nullable|string',
            'brand_id'             => 'nullable|exists:brands,id',
            'model_id'             => 'nullable|exists:brands,id',
            'reference_number'     => 'nullable|string|max:120',
            'category_id'          => 'nullable|exists:categories,id',
            'price'                => 'nullable|numeric|min:0|max:999999',
            'price_negotiable'     => 'nullable|boolean',
            'accept_traders'       => 'nullable|boolean',
            'city'                 => 'nullable|string|max:255',
            'postcode'             => 'nullable|string|max:60',
            'meeting_preference_id'=> 'nullable|exists:attribute_options,id',
            'show_phone'           => 'nullable|boolean',
            'uploaded_photos'      => 'nullable|array',
            'uploaded_photos.*'    => 'nullable|string|max:500',
            'paper_id'             => 'nullable|exists:attribute_options,id',
            'box_id'               => 'nullable|exists:attribute_options,id',
            'year_id'              => 'nullable|exists:attribute_options,id',
            'gender_id'            => 'nullable|exists:attribute_options,id',
            'condition_id'         => 'nullable|exists:attribute_options,id',
            'case_size_mm'         => 'nullable|string|max:60',
            'service_history'      => 'nullable|string',
            'movement_id'          => 'nullable|exists:attribute_options,id',
            'case_material_id'     => 'nullable|exists:attribute_options,id',
            'bracelet_material_id' => 'nullable|exists:attribute_options,id',
            'dial_colour_id'       => 'nullable|exists:attribute_options,id',
            'case_diameter_id'     => 'nullable|exists:attribute_options,id',
            'waterproof_id'        => 'nullable|exists:attribute_options,id',
        ];
    }

    private function advertDraftFields(array $validated): array
    {
        $v = fn (string $key, $default = null) => $validated[$key] ?? $default;

        return array_filter([
            'title'                => $v('title'),
            'description'          => $v('description'),
            'brand_id'             => $v('brand_id'),
            'model_id'             => $v('model_id'),
            'reference_number'     => $v('reference_number'),
            'category_id'          => $v('category_id'),
            'price'                => $v('price'),
            'price_negotiable'     => array_key_exists('price_negotiable', $validated) ? (bool) $validated['price_negotiable'] : false,
            'accept_traders'       => array_key_exists('accept_traders', $validated) ? (bool) $validated['accept_traders'] : false,
            'city'                 => $v('city'),
            'postcode'             => $v('postcode'),
            'meeting_preference_id'=> $v('meeting_preference_id'),
            'show_phone'           => array_key_exists('show_phone', $validated) ? (bool) $validated['show_phone'] : true,
            'paper_id'             => $v('paper_id'),
            'box_id'               => $v('box_id'),
            'year_id'              => $v('year_id'),
            'gender_id'            => $v('gender_id'),
            'condition_id'         => $v('condition_id'),
            'case_size_mm'         => $v('case_size_mm'),
            'service_history'      => $v('service_history'),
            'movement_id'          => $v('movement_id'),
            'case_material_id'     => $v('case_material_id'),
            'bracelet_material_id' => $v('bracelet_material_id'),
            'dial_colour_id'       => $v('dial_colour_id'),
            'case_diameter_id'     => $v('case_diameter_id'),
            'waterproof_id'        => $v('waterproof_id'),
        ], fn ($val) => $val !== null);
    }

    private function rules(bool $isCreate = true): array
    {
        return [
            'title'               => 'required|string|max:255',
            'description'         => 'required|string',
            'brand_id'            => 'required|exists:brands,id',
            'model_id'            => 'required|exists:brands,id',
            'reference_number'    => 'nullable|string|max:120',
            'category_id'         => 'nullable|exists:categories,id',
            'price'               => 'required|numeric|min:0|max:999999',
            'price_negotiable'    => 'nullable|boolean',
            'accept_traders'      => 'nullable|boolean',
            'city'                => $isCreate ? 'required|string|max:255' : 'nullable|string|max:255',
            'postcode'            => 'nullable|string|max:60',
            'meeting_preference_id'=> 'nullable|exists:attribute_options,id',
            'show_phone'          => 'nullable|boolean',
            'main_image'          => $isCreate ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120' : 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'gallery.*'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'photos'              => 'nullable|array',
            'photos.*'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'uploaded_photos'     => 'nullable|array',
            'uploaded_photos.*'   => 'nullable|string|max:500',
            'main_photo_upload'   => 'nullable|string|max:500',
            'remove_photos'       => 'nullable|array',
            'paper_id'            => 'required|exists:attribute_options,id',
            'box_id'              => 'required|exists:attribute_options,id',
            'year_id'             => 'required|exists:attribute_options,id',
            'gender_id'           => 'nullable|exists:attribute_options,id',
            'condition_id'        => 'nullable|exists:attribute_options,id',
            'case_size_mm'        => 'nullable|string|max:60',
            'service_history'     => 'nullable|string',
            'movement_id'         => 'nullable|exists:attribute_options,id',
            'case_material_id'    => 'nullable|exists:attribute_options,id',
            'bracelet_material_id'=> 'nullable|exists:attribute_options,id',
            'dial_colour_id'      => 'nullable|exists:attribute_options,id',
            'case_diameter_id'    => 'nullable|exists:attribute_options,id',
            'waterproof_id'       => 'nullable|exists:attribute_options,id',
        ];
    }

    private function advertFields(array $validated, ?string $mainImage = null, ?Advert $existingAdvert = null): array
    {
        $value = function (string $key, $default = null) use ($validated, $existingAdvert) {
            if (array_key_exists($key, $validated)) {
                return $validated[$key];
            }

            if ($existingAdvert) {
                return $existingAdvert->{$key};
            }

            return $default;
        };

        return [
            'title'               => $value('title'),
            'description'         => $value('description'),
            'brand_id'            => $value('brand_id'),
            'model_id'            => $value('model_id'),
            'reference_number'    => $value('reference_number'),
            'category_id'         => $value('category_id'),
            'price'               => $value('price', 0),
            'price_negotiable'    => array_key_exists('price_negotiable', $validated)
                ? (bool) $validated['price_negotiable']
                : ($existingAdvert ? (bool) $existingAdvert->price_negotiable : false),
            'accept_traders'      => array_key_exists('accept_traders', $validated)
                ? (bool) $validated['accept_traders']
                : ($existingAdvert ? (bool) $existingAdvert->accept_traders : false),
            'city'                => $value('city'),
            'postcode'            => $value('postcode'),
            'meeting_preference_id'=> $value('meeting_preference_id'),
            'show_phone'          => array_key_exists('show_phone', $validated)
                ? (bool) $validated['show_phone']
                : ($existingAdvert ? (bool) $existingAdvert->show_phone : true),
            'main_image'          => $mainImage,
            'paper_id'            => $value('paper_id'),
            'box_id'              => $value('box_id'),
            'year_id'             => $value('year_id'),
            'gender_id'           => $value('gender_id'),
            'condition_id'        => $value('condition_id'),
            'case_size_mm'        => $value('case_size_mm'),
            'service_history'     => $value('service_history'),
            'movement_id'         => $value('movement_id'),
            'case_material_id'    => $value('case_material_id'),
            'bracelet_material_id'=> $value('bracelet_material_id'),
            'dial_colour_id'      => $value('dial_colour_id'),
            'case_diameter_id'    => $value('case_diameter_id'),
            'waterproof_id'       => $value('waterproof_id'),
        ];
    }

    private function authorize_seller(): void
    {
        if (!auth()->user()->isSeller()) {
            abort(403, 'Only sellers can create adverts.');
        }
    }

    private function authorize_owner(Advert $advert): void
    {
        if ($advert->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    private function ensureTradeAdvertLimitAvailable()
    {
        $usage = $this->tradeAdvertUsage(auth()->user());

        if (!$usage || $usage['can_create']) {
            return null;
        }

        $message = $usage['max'] > 0
            ? "Trade package limit reached ({$usage['active_count']}/{$usage['max']} active adverts). Delete or deactivate an active advert to create a new one."
            : 'Trade package is not active. Please complete trade checkout first.';

        return redirect()->route('my-account')->with('error', $message);
    }

    private function tradeAdvertUsage($user): ?array
    {
        return $user->tradeAdvertUsage();
    }

    private function isPublishedAdvert(Advert $advert): bool
    {
        return $advert->status !== Advert::STATUS_DRAFT;
    }

    private function isPrivatePublishedAdvert(Advert $advert): bool
    {
        return auth()->user()->isPrivateSeller() && $this->isPublishedAdvert($advert);
    }

    private function privatePublishedPriceRange(Advert $advert): ?array
    {
        $order = MembershipOrder::query()
            ->with('level')
            ->where('advert_id', $advert->id)
            ->where('user_id', auth()->id())
            ->where('status', 'paid')
            ->latest('id')
            ->first();

        if (!$order || !$order->level || $order->level->seller_type !== MembershipLevel::SELLER_TYPE_PRIVATE) {
            return null;
        }

        $min = $order->level->private_min_advert_price;
        $max = $order->level->private_max_advert_price;

        if ($min === null || $max === null) {
            return null;
        }

        return ['min' => (float) $min, 'max' => (float) $max];
    }

    private function enforcePublishedAdvertRestrictions(Advert $advert, array $validated): void
    {
        if (!$this->isPublishedAdvert($advert)) {
            return;
        }

        // A title is editorial and deliberately remains editable. These fields
        // identify the listed watch and must not be changed after publication.
        $lockedFields = [
            'brand_id',
            'model_id',
            'reference_number',
            'category_id',
            'year_id',
            'gender_id',
            'case_size_mm',
            'movement_id',
            'case_material_id',
            'bracelet_material_id',
            'dial_colour_id',
            'case_diameter_id',
            'waterproof_id',
        ];

        foreach ($lockedFields as $field) {
            if (!array_key_exists($field, $validated)) {
                continue;
            }

            $newValue = $validated[$field];
            $oldValue = $advert->{$field};
            if ((string) $newValue !== (string) $oldValue) {
                throw ValidationException::withMessages([
                    $field => 'Watch identification and specifications cannot be changed after publication.',
                ]);
            }
        }

        $range = $this->privatePublishedPriceRange($advert);
        if ($range && isset($validated['price'])) {
            $price = (float) $validated['price'];
            if ($price < $range['min'] || $price > $range['max']) {
                throw ValidationException::withMessages([
                    'price' => "You can set pricing only between £{$range['min']} and £{$range['max']} for this purchased package.",
                ]);
            }
        }
    }

    /**
     * Read the photo changes staged in the edit form. New photos are already
     * uploaded to the user's drafts folder; nothing touches the advert until
     * the form is saved.
     *
     * @return array{remove: string[], newMain: ?string, uploads: string[]}
     */
    private function stagedPhotoChanges(Request $request): array
    {
        $remove = array_values(array_filter(
            array_map('strval', (array) $request->input('remove_photos', [])),
            fn ($id) => $id !== ''
        ));
        $newMain = $this->sanitizeDraftPhotoPaths([(string) $request->input('main_photo_upload', '')])[0] ?? null;
        $uploads = array_values(array_filter(
            $this->sanitizeDraftPhotoPaths((array) $request->input('uploaded_photos', [])),
            fn ($path) => $path !== $newMain
        ));

        return compact('remove', 'newMain', 'uploads');
    }

    /** Number of photos the advert will have once the staged changes are applied. */
    private function plannedPhotoCount(Request $request, Advert $advert): int
    {
        ['remove' => $remove, 'newMain' => $newMain, 'uploads' => $uploads] = $this->stagedPhotoChanges($request);

        $removedGallery = $advert->images()->whereIn('id', array_map('intval', array_diff($remove, ['main'])))->count();
        $gallery = $advert->images()->count() - $removedGallery + count($uploads);
        $hasMain = $newMain || ($advert->main_image && !in_array('main', $remove, true));

        return ($hasMain ? 1 : 0) + $gallery;
    }

    /**
     * Apply staged photo changes: gallery removals, new gallery photos, and a
     * replaced or removed main photo. When the main photo is removed without a
     * replacement, the first gallery photo is promoted. With $requireMainPhoto
     * the advert may not be left without any photo.
     */
    private function applyPhotoChanges(Request $request, Advert $advert, bool $requireMainPhoto): void
    {
        ['remove' => $remove, 'newMain' => $newMain, 'uploads' => $uploads] = $this->stagedPhotoChanges($request);

        if ($requireMainPhoto && $this->plannedPhotoCount($request, $advert) < 1) {
            throw ValidationException::withMessages([
                'remove_photos' => 'A listing needs a main photo. Add a replacement before removing its only photo.',
            ]);
        }

        foreach (array_diff($remove, ['main']) as $removeId) {
            $image = $advert->images()->find((int) $removeId);
            if ($image) {
                $this->deleteImagePath($image->image_path);
                $image->delete();
            }
        }

        $nextOrder = ($advert->images()->max('sort_order') ?? -1) + 1;
        foreach ($uploads as $path) {
            $finalPath = $this->moveDraftPhotoToFinal($path, false);
            if ($finalPath) {
                $advert->images()->create(['image_path' => $finalPath, 'sort_order' => $nextOrder++]);
            }
        }

        $newMainPath = $newMain ? $this->moveDraftPhotoToFinal($newMain, true) : null;
        if ($newMainPath) {
            $this->deleteImagePath($advert->main_image);
            $advert->update(['main_image' => $newMainPath]);
        } elseif (in_array('main', $remove, true) && $advert->main_image) {
            $this->deleteImagePath($advert->main_image);
            $advert->update(['main_image' => null]);
        }

        // Promote the first gallery photo when there is no main photo.
        if (!$advert->main_image && ($promoted = $advert->images()->first())) {
            $advert->update(['main_image' => $promoted->image_path]);
            $promoted->delete();
        }

        $advert->unsetRelation('images');
    }

    private function sanitizeDraftPhotoPaths(array $paths): array
    {
        $userId = (int) auth()->id();
        $newPrefix = "images/adverts/drafts/{$userId}/";
        $oldPrefix = "adverts/drafts/{$userId}/";

        return collect($paths)
            ->filter(fn ($path) => is_string($path) && (str_starts_with($path, $newPrefix) || str_starts_with($path, $oldPrefix)))
            ->map(fn ($path) => trim($path))
            ->filter(fn ($path) => $this->imagePathExists($path))
            ->values()
            ->all();
    }

    private function moveDraftPhotoToFinal(?string $path, bool $isMain): ?string
    {
        if (!$path) {
            return null;
        }

        if (!$this->imagePathExists($path)) {
            return null;
        }

        if (!str_starts_with($path, 'adverts/drafts/') && !str_starts_with($path, 'images/adverts/drafts/')) {
            return $path;
        }

        $target = $this->generatePublicImagePath($isMain ? 'adverts/main' : 'adverts/gallery', pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg');
        $targetFull = public_path($target);
        if (!is_dir(dirname($targetFull))) {
            @mkdir(dirname($targetFull), 0777, true);
        }

        if (str_starts_with($path, 'images/')) {
            @rename(public_path($path), $targetFull);
        } else {
            Storage::disk('public')->move($path, str_replace('images/', '', $target));
        }

        return $target;
    }

    private function storeImageInPublic(UploadedFile $file, string $folder): string
    {
        $folder = trim($folder, '/');
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $target = $this->generatePublicImagePath($folder, $ext);
        $targetFull = public_path($target);
        if (!is_dir(dirname($targetFull))) {
            @mkdir(dirname($targetFull), 0777, true);
        }
        $file->move(dirname($targetFull), basename($targetFull));

        return $target;
    }

    private function generatePublicImagePath(string $folder, string $ext): string
    {
        $filename = uniqid('', true) . '.' . ltrim($ext, '.');
        return 'images/' . trim($folder, '/') . '/' . $filename;
    }

    private function deleteImagePath(?string $path): void
    {
        if (!$path) {
            return;
        }

        $normalized = ltrim($path, '/');
        if (str_starts_with($normalized, 'images/')) {
            $full = public_path($normalized);
            if (is_file($full)) {
                @unlink($full);
            }
            return;
        }

        Storage::disk('public')->delete($normalized);
    }

    private function imagePathExists(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        $normalized = ltrim($path, '/');
        if (str_starts_with($normalized, 'images/')) {
            return is_file(public_path($normalized));
        }

        return Storage::disk('public')->exists($normalized);
    }

    private function normalizeAdvertBooleans(Request $request, array $validated): array
    {
        $validated['price_negotiable'] = $request->boolean('price_negotiable');
        $validated['accept_traders'] = $request->boolean('accept_traders');
        $validated['show_phone'] = $request->boolean('show_phone');

        return $validated;
    }
}
