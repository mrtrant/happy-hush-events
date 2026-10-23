<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventoryCategoryController extends Controller
{
    public function index()
    {
        $categories = InventoryCategory::withCount('items')->latest()->paginate(10);
        return view('admin.inventory.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:inventory_categories,name',
            'description' => 'nullable|string|max:255',
        ]);

        $validated['uuid'] = Str::uuid();
        $validated['is_active'] = $request->has('is_active');

        InventoryCategory::create($validated);

        return redirect()->route('admin.inventory.categories.index')->with('success', 'Category created successfully.');
    }

    public function update(Request $request, InventoryCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:inventory_categories,name,' . $category->id,
            'description' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $category->update($validated);

        return redirect()->route('admin.inventory.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(InventoryCategory $category)
    {
        if ($category->items()->count() > 0) {
            return back()->with('error', 'Cannot delete category with associated inventory items.');
        }

        $category->delete();
        return redirect()->route('admin.inventory.categories.index')->with('success', 'Category deleted successfully.');
    }
}

?>
