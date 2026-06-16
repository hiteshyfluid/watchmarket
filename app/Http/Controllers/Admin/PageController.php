<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    private const EDITABLE_SLUGS = [Page::SLUG_TERMS, Page::SLUG_PRIVACY];

    public function edit(Page $page): View
    {
        $this->ensureEditable($page);

        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $this->ensureEditable($page);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $page->update($validated);

        return redirect()->route('admin.pages.edit', $page)
            ->with('success', $page->title . ' updated successfully.');
    }

    private function ensureEditable(Page $page): void
    {
        if (!in_array($page->slug, self::EDITABLE_SLUGS, true)) {
            throw new NotFoundHttpException();
        }
    }
}
