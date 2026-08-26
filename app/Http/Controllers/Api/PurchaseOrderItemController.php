<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrderItemRequest;
use App\Http\Resources\PurchaseOrderItemResource;
use App\Models\ModelPurchaseOrdersItems;
use App\Models\PurchaseOrder;

class PurchaseOrderItemController extends Controller
{
    public function index(string $poId)
    {
        $this->authorize('viewAny', ModelPurchaseOrdersItems::class);
        $po = PurchaseOrder::findOrFail($poId);

        return response()->json([
            'message' => 'Item purchase order berhasil ditampilkan.',
            'data'    => PurchaseOrderItemResource::collection(
                $po->items()->with('product')->get()
            ),
        ]);
    }

    public function store(PurchaseOrderItemRequest $request, string $poId)
    {
        $this->authorize('create', ModelPurchaseOrdersItems::class);
        $po = PurchaseOrder::findOrFail($poId);

        if ($po->status !== 'draft') {
            return response()->json([
                'message' => 'Item hanya bisa ditambahkan ke PO berstatus draft.',
            ], 422);
        }

        $validated = $request->validated();
        $subtotal  = $validated['jumlah'] * $validated['harga_satuan'];

        $item = $po->items()->create([
            ...$validated,
            'subtotal' => $subtotal,
        ]);

        // Recalculate total PO
        $po->update(['total' => $po->items()->sum('subtotal')]);

        return response()->json([
            'message' => 'Item berhasil ditambahkan.',
            'data'    => new PurchaseOrderItemResource($item->load('product')),
        ], 201);
    }

    public function update(PurchaseOrderItemRequest $request, string $poId, string $itemId)
    {
        $po   = PurchaseOrder::findOrFail($poId);
        $item = ModelPurchaseOrdersItems::where('purchase_order_id', $poId)->findOrFail($itemId);
        $this->authorize('update', $item);

        if ($po->status !== 'draft') {
            return response()->json([
                'message' => 'Item hanya bisa diubah jika PO masih berstatus draft.',
            ], 422);
        }

        $validated = $request->validated();
        $subtotal  = $validated['jumlah'] * $validated['harga_satuan'];

        $item->update([
            ...$validated,
            'subtotal' => $subtotal,
        ]);

        $po->update(['total' => $po->items()->sum('subtotal')]);

        return response()->json([
            'message' => 'Item berhasil diupdate.',
            'data'    => new PurchaseOrderItemResource($item->load('product')),
        ]);
    }

    public function destroy(string $poId, string $itemId)
    {
        $po   = PurchaseOrder::findOrFail($poId);
        $item = ModelPurchaseOrdersItems::where('purchase_order_id', $poId)->findOrFail($itemId);
        $this->authorize('delete', $item);

        if ($po->status !== 'draft') {
            return response()->json([
                'message' => 'Item hanya bisa dihapus jika PO masih berstatus draft.',
            ], 422);
        }

        $item->delete();

        $po->update(['total' => $po->items()->sum('subtotal')]);

        return response()->json([
            'message' => 'Item berhasil dihapus.',
        ]);
    }
}
