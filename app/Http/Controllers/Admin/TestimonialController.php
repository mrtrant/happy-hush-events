<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('testimonials');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('approval')) {
            $query->where('is_approved', $request->integer('approval'));
        }

        $testimonials = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.form', ['testimonial' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_approved' => ['nullable', 'boolean'],
        ]);

        $path = $request->hasFile('image')
            ? $request->file('image')->store('testimonials', 'public')
            : null;

        DB::table('testimonials')->insert([
            'uuid' => (string) Str::uuid(),
            'client_id' => null,
            'name' => $validated['name'],
            'message' => $validated['message'],
            'rating' => $validated['rating'] ?? 5,
            'image_path' => $path,
            'is_approved' => $request->boolean('is_approved'),
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial created successfully.');
    }

    public function edit(int $testimonial)
    {
        $record = DB::table('testimonials')->where('id', $testimonial)->first();

        abort_unless($record, 404);

        return view('admin.testimonials.form', ['testimonial' => $record]);
    }

    public function update(Request $request, int $testimonial)
    {
        $record = DB::table('testimonials')->where('id', $testimonial)->first();

        abort_unless($record, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_approved' => ['nullable', 'boolean'],
        ]);

        $path = $record->image_path;

        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('testimonials', 'public');

            if ($path) {
                Storage::disk('public')->delete($path);
            }

            $path = $newPath;
        }

        // The testimonials table has no updated_at column.
        DB::table('testimonials')->where('id', $testimonial)->update([
            'name' => $validated['name'],
            'message' => $validated['message'],
            'rating' => $validated['rating'] ?? 5,
            'image_path' => $path,
            'is_approved' => $request->boolean('is_approved'),
        ]);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial updated successfully.');
    }

    public function toggleApproval(int $testimonial)
    {
        $record = DB::table('testimonials')->where('id', $testimonial)->first();

        abort_unless($record, 404);

        DB::table('testimonials')->where('id', $testimonial)->update([
            'is_approved' => ! $record->is_approved,
        ]);

        return back()->with('success', 'Testimonial approval status updated.');
    }

    public function destroy(int $testimonial)
    {
        $record = DB::table('testimonials')->where('id', $testimonial)->first();

        abort_unless($record, 404);

        DB::table('testimonials')->where('id', $testimonial)->delete();

        if ($record->image_path) {
            Storage::disk('public')->delete($record->image_path);
        }

        return back()->with('success', 'Testimonial deleted successfully.');
    }
}
