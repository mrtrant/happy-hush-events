<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryCategory;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class InventoryItemController extends Controller
{
    /**
     * Display a listing of the inventory items.
     */
    public function index(Request $request)
    {
        $query = InventoryItem::with('category');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('inventory_code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $items = $query->latest()->paginate(10)->withQueryString();
        $categories = InventoryCategory::where('is_active', true)->get();

        return view('admin.inventory.items.index', compact('items', 'categories'));
    }

    /**
     * Show the form for creating a new inventory item.
     */
    public function create()
    {
        $categories = InventoryCategory::where('is_active', true)->get();
        return view('admin.inventory.items.create', compact('categories'));
    }

    /**
     * Store a newly created inventory item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_code' => 'required|string|max:30|unique:inventory_items,inventory_code',
            'category_id' => 'required|exists:inventory_categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'unit_type' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'condition_status' => 'required|in:New,Good,Needs Repair,Damaged,Retired',
            'location' => 'nullable|string|max:150',
            'image' => 'nullable|image|max:2048'
        ]);

        DB::transaction(function () use ($request, &$validated) {
            $validated['uuid'] = Str::uuid();
            $validated['available_quantity'] = $validated['quantity'];
            $validated['is_active'] = $request->has('is_active');

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('inventory', 'public');
            }

            $item = InventoryItem::create($validated);

            // Log initial stock addition
            InventoryLog::create([
                'uuid' => Str::uuid(),
                'inventory_item_id' => $item->id,
                'action' => 'Stock Added',
                'quantity_before' => 0,
                'quantity_change' => $item->quantity,
                'quantity_after' => $item->quantity,
                'notes' => 'Initial stock entry',
                'created_by' => auth()->id()
            ]);
        });

        return redirect()->route('admin.inventory.items.index')->with('success', 'Inventory item created successfully.');
    }

    /**
     * Show the form for editing the specified inventory item.
     */
    public function edit(InventoryItem $item)
    {
        $categories = InventoryCategory::where('is_active', true)->get();
        return view('admin.inventory.items.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified inventory item in storage.
     */
    public function update(Request $request, InventoryItem $item)
    {
        $validated = $request->validate([
            'inventory_code' => 'required|string|max:30|unique:inventory_items,inventory_code,' . $item->id,
            'category_id' => 'required|exists:inventory_categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'unit_type' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'condition_status' => 'required|in:New,Good,Needs Repair,Damaged,Retired',
            'location' => 'nullable|string|max:150',
            'image' => 'nullable|image|max:2048'
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            // Delete old image from storage if it exists
            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }
            $validated['image'] = $request->file('image')->store('inventory', 'public');
        }

        $item->update($validated);

        return redirect()->route('admin.inventory.items.index')->with('success', 'Inventory item updated successfully.');
    }
}
