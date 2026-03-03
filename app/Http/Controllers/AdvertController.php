<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\AdvertImage;
use App\Models\AttributeOption;
use App\Models\Brand;
use App\Models\Category;
use App\Models\MembershipLevel;
use App\Models\MembershipOrder;
use App\Models\MembershipSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AdvertController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $this->pauseExpiredAdvertsForUser($user->id);

        $adverts = $user->adverts()
            ->with(['brand', 'model', 'condition'])
            ->latest()
            ->paginate(15);

        $tradeUsage = $this->tradeAdvertUsage($user);

        return view('adverts.index', compact('adverts', 'tradeUsage'));
    }

    public function create()
    {
        if ($redirect = $this->ensureTradeAdvertLimitAvailable()) {
            return $redirect;
        }

        $data = $this->formData();
        return view('adverts.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize_seller();
        if ($redirect = $this->ensureTradeAdvertLimitAvailable()) {
            return $redirect;
        }

        $validated = $request->validate($this->rules());

        $photoFiles = $request->file('photos', []);
        $uploadedPhotoPaths = $this->sanitizeDraftPhotoPaths((array) $request->input('uploaded_photos', []));
        $uploadedPhotoCount = count($uploadedPhotoPaths);
        $photoCount = max(count($photoFiles), $uploadedPhotoCount);

        if ($photoCount < 5) {
            throw ValidationException::withMessages([
                'photos' => 'Please upload at least 5 images.',
            ]);
        }

        $mainImagePath = null;

        if (!empty($photoFiles)) {
            $mainImagePath = $photoFiles[0]->store('adverts/main', 'public');
        } elseif (!empty($uploadedPhotoPaths)) {
            $mainImagePath = $this->moveDraftPhotoToFinal(array_shift($uploadedPhotoPaths), true);
        } elseif ($request->hasFile('main_image')) {
            // Fallback for legacy form submissions.
            $mainImagePath = $request->file('main_image')->store('adverts/main', 'public');
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
                $path = $file->store('adverts/gallery', 'public');
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
                $path = $file->store('adverts/gallery', 'public');
                $advert->images()->create(['image_path' => $path, 'sort_order' => $index]);
            }
        }

        if ($isPrivateSeller) {
            return redirect()->route('seller.private.packages', $advert)
                ->with('success', 'Advert details saved. Please complete package checkout to activate this advert.');
        }

        return redirect()->route('adverts.index')->with('success', 'Advert created successfully!');
    }

    public function edit(Advert $advert)
    {
        $this->authorize_owner($advert);
        $data = $this->formData();
        $privateEditLock = $this->isPrivatePublishedAdvert($advert);
        $privatePriceRange = $privateEditLock ? $this->privatePublishedPriceRange($advert) : null;

        return view('adverts.edit', array_merge($data, compact('advert', 'privateEditLock', 'privatePriceRange')));
    }

    public function update(Request $request, Advert $advert)
    {
        $this->authorize_owner($advert);

        $validated = $request->validate($this->rules(false));
        $this->enforcePrivatePublishedRestrictions($advert, $validated);

        // Replace main image if new one uploaded
        if ($request->hasFile('main_image')) {
            if ($advert->main_image) {
                Storage::disk('public')->delete($advert->main_image);
            }
            $validated['main_image'] = $request->file('main_image')->store('adverts/main', 'public');
        }

        $advert->update($this->advertFields($validated, isset($validated['main_image']) ? $validated['main_image'] : $advert->main_image, $advert));
        if (auth()->user()->isTradeSeller()) {
            $advert->update(['expiry_date' => null]);
        }

        // Append new gallery images
        if ($request->hasFile('gallery')) {
            $nextOrder = $advert->images()->max('sort_order') + 1;
            foreach ($request->file('gallery') as $index => $file) {
                $path = $file->store('adverts/gallery', 'public');
                $advert->images()->create(['image_path' => $path, 'sort_order' => $nextOrder + $index]);
            }
        }

        return redirect()->route('adverts.index')
            ->with('success', 'Advert updated successfully!');
    }

    public function destroy(Advert $advert)
    {
        $this->authorize_owner($advert);

        // Delete stored images
        if ($advert->main_image) {
            Storage::disk('public')->delete($advert->main_image);
        }
        foreach ($advert->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        $advert->delete();

        return redirect()->route('adverts.index')
            ->with('success', 'Advert deleted.');
    }

    // ----------------------------------------------------------------
    // Delete a single gallery image (called via POST from edit form)
    // ----------------------------------------------------------------
    public function deleteImage(Request $request, Advert $advert, AdvertImage $image)
    {
        $this->authorize_owner($advert);

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    public function togglePause(Advert $advert)
    {
        $this->authorize_owner($advert);

        if ($advert->status === Advert::STATUS_ACTIVE) {
            $advert->update(['status' => Advert::STATUS_PAUSED]);
            return back()->with('success', 'Advert paused.');
        }

        if ($advert->status === Advert::STATUS_PAUSED) {
            $advert->update(['status' => Advert::STATUS_ACTIVE]);
            return back()->with('success', 'Advert activated.');
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
        $path = $request->file('photo')->store("adverts/drafts/{$userId}", 'public');

        return response()->json([
            'ok' => true,
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    public function deleteDraftPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = (string) $request->input('path');
        $userId = (int) auth()->id();
        $prefix = "adverts/drafts/{$userId}/";

        if (str_starts_with($path, $prefix) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
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

    private function rules(bool $isCreate = true): array
    {
        return [
            'title'               => 'required|string|max:255',
            'description'         => 'required|string',
            'brand_id'            => 'required|exists:brands,id',
            'model_id'            => 'required|exists:brands,id',
            'reference_number'    => 'nullable|string|max:120',
            'category_id'         => 'nullable|exists:categories,id',
            'price'               => 'required|numeric|min:0',
            'price_negotiable'    => 'nullable|boolean',
            'accept_traders'      => 'nullable|boolean',
            'city'                => $isCreate ? 'required|string|max:255' : 'nullable|string|max:255',
            'postcode'            => 'nullable|string|max:60',
            'meeting_preference_id'=> 'nullable|exists:attribute_options,id',
            'show_phone'          => 'nullable|boolean',
            'main_image'          => $isCreate ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120' : 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'gallery.*'           => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'photos'              => 'nullable|array|max:20',
            'photos.*'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'uploaded_photos'     => 'nullable|array|max:20',
            'uploaded_photos.*'   => 'nullable|string|max:500',
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

        return redirect()->route('adverts.index')->with('error', $message);
    }

    private function tradeAdvertUsage($user): ?array
    {
        if (!$user->isTradeSeller()) {
            return null;
        }

        $subscription = MembershipSubscription::query()
            ->with('level')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        $activeCount = Advert::query()
            ->where('user_id', $user->id)
            ->where('status', Advert::STATUS_ACTIVE)
            ->count();

        if (!$subscription || !$subscription->level || $subscription->level->seller_type !== MembershipLevel::SELLER_TYPE_TRADE) {
            return [
                'active_count' => $activeCount,
                'max' => 0,
                'unlimited' => false,
                'can_create' => false,
                'display' => "{$activeCount}/0",
            ];
        }

        $max = (int) $subscription->level->trade_max_advert_count;
        $unlimited = $max === -1;
        $canCreate = $unlimited || $activeCount < $max;

        return [
            'active_count' => $activeCount,
            'max' => $max,
            'unlimited' => $unlimited,
            'can_create' => $canCreate,
            'display' => $unlimited ? "{$activeCount}/Unlimited" : "{$activeCount}/{$max}",
        ];
    }

    private function pauseExpiredAdvertsForUser(int $userId): void
    {
        Advert::query()
            ->where('user_id', $userId)
            ->where('status', Advert::STATUS_ACTIVE)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now()->toDateString())
            ->update(['status' => Advert::STATUS_PAUSED]);
    }

    private function isPrivatePublishedAdvert(Advert $advert): bool
    {
        return auth()->user()->isPrivateSeller() && $advert->status !== Advert::STATUS_DRAFT;
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

    private function enforcePrivatePublishedRestrictions(Advert $advert, array $validated): void
    {
        if (!$this->isPrivatePublishedAdvert($advert)) {
            return;
        }

        foreach (['title', 'brand_id', 'model_id', 'year_id'] as $field) {
            $newValue = $validated[$field] ?? null;
            $oldValue = $advert->{$field};
            if ((string) $newValue !== (string) $oldValue) {
                throw ValidationException::withMessages([
                    $field => 'For published private adverts, title, brand, model, and year cannot be changed.',
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

    private function sanitizeDraftPhotoPaths(array $paths): array
    {
        $userId = (int) auth()->id();
        $prefix = "adverts/drafts/{$userId}/";

        return collect($paths)
            ->filter(fn ($path) => is_string($path) && str_starts_with($path, $prefix))
            ->map(fn ($path) => trim($path))
            ->filter(fn ($path) => Storage::disk('public')->exists($path))
            ->values()
            ->all();
    }

    private function moveDraftPhotoToFinal(?string $path, bool $isMain): ?string
    {
        if (!$path) {
            return null;
        }

        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        if (!str_starts_with($path, 'adverts/drafts/')) {
            return $path;
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';
        $filename = uniqid('', true) . '.' . $ext;
        $target = $isMain ? "adverts/main/{$filename}" : "adverts/gallery/{$filename}";

        Storage::disk('public')->move($path, $target);

        return $target;
    }
}
