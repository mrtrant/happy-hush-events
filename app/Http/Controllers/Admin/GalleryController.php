<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('gallery')
            ->leftJoin('gallery_categories', 'gallery.category_id', '=', 'gallery_categories.id')
            ->leftJoin('users', 'gallery.uploaded_by', '=', 'users.id')
            ->select(
                'gallery.*',
                'gallery_categories.name as category_name',
                DB::raw("TRIM(CONCAT(users.first_name, ' ', COALESCE(users.middle_name, ''), ' ', users.last_name)) as uploader_name")
            );

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('gallery.title', 'like', "%{$search}%")
                    ->orWhere('gallery.description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('gallery.category_id', $request->integer('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('gallery.is_active', $request->integer('status'));
        }

        $images = $query->orderByDesc('gallery.created_at')
            ->paginate(12)
            ->withQueryString();

        $categories = DB::table('gallery_categories')
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('admin.gallery.index', compact('images', 'categories'));
    }

    public function create()
    {
        $categories = DB::table('gallery_categories')
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('admin.gallery.form', [
            'image' => null,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:gallery_categories,id'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $path = $request->file('image')->store('gallery', 'public');

        DB::table('gallery')->insert([
            'uuid' => (string) Str::uuid(),
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_path' => $path,
            'uploaded_by' => auth()->id(),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery image uploaded successfully.');
    }

    public function edit(int $image)
    {
        $galleryImage = DB::table('gallery')->where('id', $image)->first();

        abort_unless($galleryImage, 404);

        $categories = DB::table('gallery_categories')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('admin.gallery.form', [
            'image' => $galleryImage,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, int $image)
    {
        $galleryImage = DB::table('gallery')->where('id', $image)->first();

        abort_unless($galleryImage, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:gallery_categories,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $path = $galleryImage->image_path;

        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('gallery', 'public');

            if ($path) {
                Storage::disk('public')->delete($path);
            }

            $path = $newPath;
        }

        DB::table('gallery')->where('id', $image)->update([
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_path' => $path,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery image updated successfully.');
    }

    public function toggleFeatured(int $image)
    {
        $record = DB::table('gallery')->where('id', $image)->first();

        abort_unless($record, 404);

        DB::table('gallery')->where('id', $image)->update([
            'is_featured' => ! $record->is_featured,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Featured status updated.');
    }

    public function toggleStatus(int $image)
    {
        $record = DB::table('gallery')->where('id', $image)->first();

        abort_unless($record, 404);

        DB::table('gallery')->where('id', $image)->update([
            'is_active' => ! $record->is_active,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Gallery visibility updated.');
    }

    public function destroy(int $image)
    {
        $record = DB::table('gallery')->where('id', $image)->first();

        abort_unless($record, 404);

        DB::table('gallery')->where('id', $image)->delete();

        if ($record->image_path) {
            Storage::disk('public')->delete($record->image_path);
        }

        return back()->with('success', 'Gallery image deleted successfully.');
    }
}
