<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrderRequest;
use App\Http\Resources\PurchaseOrderCollection;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\ModelStockMovements;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', PurchaseOrder::class);
        $pos = PurchaseOrder::with(['supplier', 'user', 'items.product'])
            ->when($request->search, fn($q) =>
                $q->where('nomor_po', 'like', "%{$request->search}%")
            )
            ->when($request->supplier_id, fn($q) =>
                $q->where('supplier_id', $request->supplier_id)
            )
            ->when($request->status, fn($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->tanggal_dari, fn($q) =>
                $q->whereDate('tanggal', '>=', $request->tanggal_dari)
            )
            ->when($request->tanggal_sampai, fn($q) =>
                $q->whereDate('tanggal', '<=', $request->tanggal_sampai)
            )
            ->paginate(10);

        return (new PurchaseOrderCollection($pos))->additional([
            'message' => 'Purchase order berhasil ditampilkan.',
        ]);
    }

    public function store(PurchaseOrderRequest $request)
    {
        $this->authorize('create', PurchaseOrder::class);
        $po = PurchaseOrder::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
            'total'   => 0,
        ]);

        return response()->json([
            'message' => 'Purchase order berhasil dibuat.',
            'data'    => new PurchaseOrderResource($po->load(['supplier', 'user'])),
        ], 201);
    }

    public function show(string $id)
    {
        $po = PurchaseOrder::with(['supplier', 'user', 'items.product'])
            ->findOrFail($id);
        $this->authorize('view', $po);

        return response()->json([
            'message' => 'Purchase order berhasil ditampilkan.',
            'data'    => new PurchaseOrderResource($po),
        ]);
    }

    public function update(PurchaseOrderRequest $request, string $id)
    {
        $po = PurchaseOrder::with('items.product')->findOrFail($id);
        $this->authorize('update', $po);

        if ($request->status === 'diterima' && $po->status !== 'diterima') {
            DB::transaction(function () use ($po) {
                foreach ($po->items as $item) {
                    $product  = $item->product;
                    $stokBaru = $product->stok + $item->jumlah;

                    ModelStockMovements::create([
                        'product_id'   => $product->id,
                        'tipe'         => 'masuk',
                        'jumlah'       => $item->jumlah,
                        'stok_sebelum' => $product->stok,
                        'stok_sesudah' => $stokBaru,
                        'referensi'    => $po->nomor_po,
                        'keterangan'   => 'Barang diterima dari PO ' . $po->nomor_po,
                        'user_id'      => auth()->id(),
                    ]);

                    $product->update(['stok' => $stokBaru]);
                }
            });
        }

        $po->update($request->validated());

        return response()->json([
            'message' => 'Purchase order berhasil diupdate.',
            'data'    => new PurchaseOrderResource($po->load(['supplier', 'user', 'items.product'])),
        ]);
    }

    public function destroy(string $id)
    {
        $po = PurchaseOrder::findOrFail($id);
        $this->authorize('delete', $po);

        if ($po->status !== 'draft') {
            return response()->json([
                'message' => 'Hanya purchase order berstatus draft yang dapat dihapus.',
            ], 422);
        }

        $po->delete();

        return response()->json([
            'message' => 'Purchase order berhasil dihapus.',
        ]);
    }
}
