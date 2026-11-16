<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        return view('tailor.inventory', [
            'items' => InventoryItem::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'unit' => ['required', 'string', 'max:20'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'low_stock_threshold' => ['required', 'numeric', 'min:0'],
        ]);

        InventoryItem::create($data);

        return back()->with('success', 'Item added to inventory.');
    }

    public function update(Request $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0'],
            'low_stock_threshold' => ['required', 'numeric', 'min:0'],
        ]);

        $inventoryItem->update($data);

        return back()->with('success', 'Stock updated.');
    }

    public function destroy(InventoryItem $inventoryItem): RedirectResponse
    {
        $inventoryItem->delete();

        return back()->with('success', 'Item removed.');
    }
}
