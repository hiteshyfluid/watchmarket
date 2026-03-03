<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class SeoMetaController extends Controller
{
    public function index()
    {
        $routes = collect(Route::getRoutes()->getRoutes())
            ->filter(function ($route) {
                $name = $route->getName();
                $methods = $route->methods();

                return $name
                    && in_array('GET', $methods, true)
                    && !str_starts_with($name, 'debugbar.')
                    && !str_starts_with($name, 'ignition.');
            })
            ->map(function ($route) {
                return [
                    'name' => $route->getName(),
                    'uri' => '/' . ltrim($route->uri(), '/'),
                ];
            })
            ->unique('name')
            ->sortBy('name')
            ->values();

        $existing = SeoMeta::query()
            ->orderBy('route_name')
            ->get()
            ->keyBy('route_name');

        return view('admin.seo.index', compact('routes', 'existing'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'entries' => ['nullable', 'array'],
            'entries.*.meta_title' => ['nullable', 'string', 'max:255'],
            'entries.*.meta_description' => ['nullable', 'string', 'max:1000'],
        ]);

        $entries = $validated['entries'] ?? [];

        $knownRouteNames = collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter()
            ->unique()
            ->values()
            ->all();

        foreach ($entries as $routeName => $data) {
            if (!in_array($routeName, $knownRouteNames, true)) {
                continue;
            }

            $title = trim((string) ($data['meta_title'] ?? ''));
            $description = trim((string) ($data['meta_description'] ?? ''));

            if ($title === '' && $description === '') {
                SeoMeta::query()->where('route_name', $routeName)->delete();
                continue;
            }

            SeoMeta::updateOrCreate(
                ['route_name' => $routeName],
                [
                    'meta_title' => $title !== '' ? $title : null,
                    'meta_description' => $description !== '' ? $description : null,
                    'is_active' => true,
                ]
            );
        }

        return redirect()
            ->route('admin.seo.index')
            ->with('success', 'SEO meta tags updated successfully.');
    }
}

