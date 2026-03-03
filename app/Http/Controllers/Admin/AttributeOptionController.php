<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttributeOption;
use Illuminate\Http\Request;

class AttributeOptionController extends Controller
{
    public function index(string $type)
    {
        abort_unless(AttributeOption::isValidType($type), 404);

        $options   = AttributeOption::ofType($type)->orderBy('sort_order')->orderBy('name')->get();
        $typeLabel = AttributeOption::typeLabel($type);

        return view('admin.attributes.index', compact('options', 'type', 'typeLabel'));
    }

    public function store(Request $request, string $type)
    {
        abort_unless(AttributeOption::isValidType($type), 404);

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        AttributeOption::create([
            'type'       => $type,
            'name'       => $validated['name'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active'  => true,
        ]);

        return redirect()->route('admin.attributes.index', $type)
            ->with('success', 'Option added.');
    }

    public function update(Request $request, string $type, AttributeOption $option)
    {
        abort_unless(AttributeOption::isValidType($type), 404);

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $option->update([
            'name'       => $validated['name'],
            'sort_order' => $validated['sort_order'] ?? $option->sort_order,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.attributes.index', $type)
            ->with('success', 'Option updated.');
    }

    public function destroy(string $type, AttributeOption $option)
    {
        $option->delete();
        return redirect()->route('admin.attributes.index', $type)
            ->with('success', 'Option deleted.');
    }
}
