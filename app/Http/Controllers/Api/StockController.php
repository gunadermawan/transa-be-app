<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockHistory;
use Illuminate\Http\Request;

class StockController extends Controller
{
    // add stock
    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|string',
            'note' => 'required|string',
        ]);

        // Use query() to respect global scope
        $stock = Stock::query()->with('outlet')->findOrFail($id);

        // Verify stock belongs to user's business via outlet
        if ($stock->outlet->business_id !== $request->user()->business_id) {
            return response()->json([
                'message' => 'Unauthorized access to this stock',
            ], 403);
        }

        if ($request->type == 'add') {
            $stock->quantity += $request->quantity;
        } else {
            $stock->quantity -= $request->quantity;
        }
        $stock->save();

        $history = StockHistory::create([
            'stock_id' => $stock->id,
            'quantity' => $request->quantity,
            'current_stock' => $stock->quantity,
            'type' => $request->type,
            'reference' => $request->reference ?? '',
            'user_id' => $request->user()->id,
            'note' => $request->note,
        ]);

        return response()->json([
            'message' => 'Stock updated successfully',
            'data' => $history,
        ], 200);
    }
}
