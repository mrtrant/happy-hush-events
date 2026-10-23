<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GalleryCategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('gallery_categories')
            ->leftJoin('gallery', 'gallery.category_id', '=', 'gallery_categories.id')
            ->select(
                'gallery_categories.*',
                DB::raw('COUNT(gallery.id) as images_count')
            )
            ->groupBy('gallery_categories.id')
            ->orderBy('gallery_categories.display_order')
            ->orderBy('gallery_categories.name')
            ->get();

        return view('admin.gallery.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:gallery_categories,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::table('gallery_categories')->insert([
            'uuid' => (string) Str::uuid(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.gallery.categories.index')
            ->with('success', 'Gallery category created successfully.');
    }

    public function update(Request $request, int $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('gallery_categories', 'name')->ignore($category),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $exists = DB::table('gallery_categories')->where('id', $category)->exists();

        abort_unless($exists, 404);

        DB::table('gallery_categories')->where('id', $category)->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.gallery.categories.index')
            ->with('success', 'Gallery category updated successfully.');
    }

    public function destroy(int $category)
    {
        $exists = DB::table('gallery_categories')->where('id', $category)->exists();

        abort_unless($exists, 404);

        if (DB::table('gallery')->where('category_id', $category)->exists()) {
            return back()->with('error', 'This category is in use. Reassign its gallery images before deleting it.');
        }

        DB::table('gallery_categories')->where('id', $category)->delete();

        return back()->with('success', 'Gallery category deleted successfully.');
    }
}
