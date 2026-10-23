<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('faqs');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->integer('status'));
        }

        $faqs = $query->orderBy('display_order')
            ->orderBy('question')
            ->paginate(15)
            ->withQueryString();

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faqs.form', ['faq' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::table('faqs')->insert([
            'uuid' => (string) Str::uuid(),
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ created successfully.');
    }

    public function edit(int $faq)
    {
        $record = DB::table('faqs')->where('id', $faq)->first();
        abort_unless($record, 404);

        return view('admin.faqs.form', ['faq' => $record]);
    }

    public function update(Request $request, int $faq)
    {
        $record = DB::table('faqs')->where('id', $faq)->first();
        abort_unless($record, 404);

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::table('faqs')->where('id', $faq)->update([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    public function toggleStatus(int $faq)
    {
        $record = DB::table('faqs')->where('id', $faq)->first();
        abort_unless($record, 404);

        DB::table('faqs')->where('id', $faq)->update([
            'is_active' => ! $record->is_active,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'FAQ status updated.');
    }

    public function destroy(int $faq)
    {
        $record = DB::table('faqs')->where('id', $faq)->first();
        abort_unless($record, 404);

        DB::table('faqs')->where('id', $faq)->delete();

        return back()->with('success', 'FAQ deleted successfully.');
    }
}
