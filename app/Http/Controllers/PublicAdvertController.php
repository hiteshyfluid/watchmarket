<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\AttributeOption;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicAdvertController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));
        $brandName = trim((string) $request->input('brand', ''));
        $brandIds = collect((array) $request->input('brands', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();
        $modelIds = collect((array) $request->input('models', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();
        $conditionIds = collect((array) $request->input('conditions', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();
        $minPrice = $request->filled('min_price') ? (float) $request->input('min_price') : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->input('max_price') : null;
        $distance = $request->filled('distance') ? (int) $request->input('distance') : 50;
        $sellerType = (string) $request->input('seller_type', 'all');
        $sort = (string) $request->input('sort', 'newest');

        $query = Advert::with(['user', 'brand', 'model', 'year', 'condition', 'paper', 'box'])
            ->where('status', Advert::STATUS_ACTIVE);

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', "%{$q}%")
                    ->orWhereHas('brand', fn ($brand) => $brand->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('model', fn ($model) => $model->where('name', 'like', "%{$q}%"));
            });
        }

        if ($brandName !== '') {
            $query->whereHas('brand', fn ($brand) => $brand->where('name', 'like', "%{$brandName}%"));
        }

        if (!empty($brandIds)) {
            $query->whereIn('brand_id', $brandIds);
        }

        if (!empty($modelIds)) {
            $query->whereIn('model_id', $modelIds);
        }

        if (!empty($conditionIds)) {
            $query->whereIn('condition_id', $conditionIds);
        }

        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null && $maxPrice > 0) {
            $query->where('price', '<=', $maxPrice);
        }

        if ($request->boolean('box_papers')) {
            $query->whereNotNull('box_id')->whereNotNull('paper_id');
        }

        if ($sellerType === 'trade') {
            $query->whereHas('user', fn ($user) => $user->where('role', User::ROLE_TRADE_SELLER));
        }
        if ($sellerType === 'private') {
            $query->whereHas('user', fn ($user) => $user->where('role', User::ROLE_PRIVATE_SELLER));
        }

        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc')->orderByDesc('id'),
            'price_desc' => $query->orderBy('price', 'desc')->orderByDesc('id'),
            default => $query->latest(),
        };

        $adverts = $query->paginate(12)->withQueryString();

        $brands = Brand::parents()->active()->orderByDesc('is_popular')->orderBy('name')->get(['id', 'name', 'is_popular']);
        $models = Brand::query()
            ->from('brands as model')
            ->select('model.id', 'model.name', 'model.parent_id')
            ->join('brands as parent', 'parent.id', '=', 'model.parent_id')
            ->where('model.is_active', true)
            ->where('parent.is_active', true)
            ->orderByDesc('parent.is_popular')
            ->orderByDesc('model.is_popular')
            ->orderBy('model.name')
            ->get();
        $conditions = AttributeOption::ofType('condition')->active()->ordered()->get(['id', 'name']);

        $selectedBrandChips = $brands->whereIn('id', $brandIds)
            ->map(fn ($brand) => ['type' => 'id', 'id' => $brand->id, 'label' => $brand->name])
            ->values();

        if ($brandName !== '') {
            $exists = $selectedBrandChips->contains(fn ($chip) => strcasecmp($chip['label'], $brandName) === 0);
            if (!$exists) {
                $selectedBrandChips->push(['type' => 'name', 'id' => null, 'label' => $brandName]);
            }
        }

        return view('market.index', compact(
            'adverts',
            'brands',
            'models',
            'conditions',
            'sort',
            'q',
            'brandIds',
            'modelIds',
            'conditionIds',
            'minPrice',
            'maxPrice',
            'distance',
            'sellerType',
            'selectedBrandChips'
        ));
    }

    public function show(Advert $advert): View
    {
        abort_unless($advert->status === Advert::STATUS_ACTIVE, 404);

        $advert->loadMissing([
            'user',
            'brand',
            'model',
            'category',
            'images',
            'paper',
            'box',
            'year',
            'condition',
            'movement',
            'caseMaterial',
            'braceletMaterial',
            'dialColour',
            'caseDiameter',
            'waterproof',
        ]);

        return view('market.show', compact('advert'));
    }
}
