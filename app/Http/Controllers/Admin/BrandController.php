<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $brands = Brand::with(['children' => function ($query) {
                $query->orderBy('sort_order')->orderBy('name');
            }])
            ->whereNull('parent_id')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhereHas('children', function ($childQuery) use ($search) {
                            $childQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        return view('admin.brands.index', compact('brands', 'search'));
    }

    public function create()
    {
        $parents = Brand::parents()->active()->orderBy('name')->get();
        return view('admin.brands.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:150',
            'slug'       => 'nullable|string|max:160|unique:brands,slug',
            'parent_id'  => 'nullable|exists:brands,id',
            'is_active'  => 'nullable|boolean',
            'is_featured'=> 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);
        $validated['is_popular'] = $request->boolean('is_popular', false);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Only top-level brands can be featured in homepage brand strip.
        if (!empty($validated['parent_id'])) {
            $validated['is_featured'] = false;
            $validated['is_popular'] = false;
        }

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->storeImageInPublic($request->file('image'), 'brands');
        }

        Brand::create($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand saved successfully.');
    }

    public function edit(Brand $brand)
    {
        $parents = Brand::parents()->active()->where('id', '!=', $brand->id)->orderBy('name')->get();
        return view('admin.brands.edit', compact('brand', 'parents'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:150',
            'slug'       => 'nullable|string|max:160|unique:brands,slug,' . $brand->id,
            'parent_id'  => 'nullable|exists:brands,id',
            'is_active'  => 'nullable|boolean',
            'is_featured'=> 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);
        $validated['is_popular'] = $request->boolean('is_popular', false);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if (!empty($validated['parent_id'])) {
            $validated['is_featured'] = false;
            $validated['is_popular'] = false;
        }

        if ($request->hasFile('image')) {
            if ($brand->image_path) {
                $this->deleteImagePath($brand->image_path);
            }
            $validated['image_path'] = $this->storeImageInPublic($request->file('image'), 'brands');
        }

        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->image_path) {
            $this->deleteImagePath($brand->image_path);
        }
        $brand->children()->update(['parent_id' => null]);
        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand deleted.');
    }

    private function storeImageInPublic(UploadedFile $file, string $folder): string
    {
        $folder = trim($folder, '/');
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = uniqid('', true) . '.' . ltrim($ext, '.');
        $relative = 'images/' . $folder . '/' . $filename;
        $fullPath = public_path($relative);
        if (!is_dir(dirname($fullPath))) {
            @mkdir(dirname($fullPath), 0777, true);
        }
        $file->move(dirname($fullPath), basename($fullPath));

        return $relative;
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
}
