<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use Illuminate\Http\Request;

class AdvertController extends Controller
{
    public function index(Request $request)
    {
        $query = Advert::with(['user', 'brand', 'model', 'condition', 'images'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $adverts = $query->paginate(20)->withQueryString();

        return view('admin.adverts.index', compact('adverts'));
    }

    public function show(Advert $advert)
    {
        $advert->load([
            'user',
            'brand',
            'model',
            'category',
            'images',
            'paper',
            'box',
            'year',
            'gender',
            'condition',
            'movement',
            'caseMaterial',
            'braceletMaterial',
            'dialColour',
            'caseDiameter',
            'waterproof',
        ]);

        return view('admin.adverts.show', compact('advert'));
    }

    public function edit(Advert $advert)
    {
        $advert->load([
            'user',
            'brand',
            'model',
            'category',
            'images',
        ]);

        return view('admin.adverts.edit', compact('advert'));
    }

    public function update(Request $request, Advert $advert)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,paused,sold,expired',
            'is_featured' => 'nullable|boolean',
        ]);

        $advert->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'status' => $validated['status'],
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return redirect()->route('admin.adverts.index')->with('success', 'Advert updated successfully.');
    }

    public function updateStatus(Request $request, Advert $advert)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,active,paused,sold,expired',
        ]);

        $advert->update(['status' => $validated['status']]);

        return back()->with('success', 'Advert status updated.');
    }

    public function updateFeatured(Request $request, Advert $advert)
    {
        $validated = $request->validate([
            'is_featured' => 'required|boolean',
        ]);

        $advert->update(['is_featured' => (bool) $validated['is_featured']]);

        return back()->with('success', 'Advert featured flag updated.');
    }

    public function destroy(Advert $advert)
    {
        $advert->delete();
        return back()->with('success', 'Advert deleted.');
    }
}
