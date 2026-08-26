<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleItemRequest;
use App\Http\Resources\SaleItemResource;
use App\Models\ModelSales;
use App\Models\ModelSalesItems;
use App\Models\ModelStockMovements;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SaleItemController extends Controller
{
    public function index(string $saleId)
    {
        $this->authorize('viewAny', ModelSalesItems::class);
        $sale = ModelSales::findOrFail($saleId);

        return response()->json([
            'message' => 'Item transaksi berhasil ditampilkan.',
            'data'    => SaleItemResource::collection(
                $sale->salesItems()->with('product')->get()
            ),
        ]);
    }

    public function store(SaleItemRequest $request, string $saleId)
    {
        $this->authorize('create', ModelSalesItems::class);
        $sale    = ModelSales::findOrFail($saleId);
        $product = Product::findOrFail($request->product_id);

        if ($sale->status !== 'pending') {
            return response()->json([
                'message' => 'Item hanya bisa ditambahkan ke transaksi berstatus pending.',
            ], 422);
        }

        if ($product->stok < $request->jumlah) {
            return response()->json([
                'message' => "Stok {$product->nama_produk} tidak mencukupi. Stok tersedia: {$product->stok}",
            ], 422);
        }

        DB::transaction(function () use ($sale, $request, $product) {
            $subtotal = $request->jumlah * $request->harga_satuan;
            $stokBaru = $product->stok - $request->jumlah;

            $sale->salesItems()->create([
                ...$request->validated(),
                'subtotal' => $subtotal,
            ]);

            ModelStockMovements::create([
                'product_id'   => $product->id,
                'tipe'         => 'keluar',
                'jumlah'       => $request->jumlah,
                'stok_sebelum' => $product->stok,
                'stok_sesudah' => $stokBaru,
                'referensi'    => $sale->nomor_transaksi,
                'keterangan'   => 'Penjualan ' . $sale->nomor_transaksi,
                'user_id'      => auth()->id(),
            ]);

            $product->update(['stok' => $stokBaru]);
            $sale->update(['total' => $sale->salesItems()->sum('subtotal')]);
        });

        return response()->json([
            'message' => 'Item berhasil ditambahkan.',
            'data'    => new SaleItemResource(
                $sale->salesItems()->with('product')->latest()->first()
            ),
        ], 201);
    }

    public function destroy(string $saleId, string $itemId)
    {
        $sale = ModelSales::findOrFail($saleId);
        $item = ModelSalesItems::where('sale_id', $saleId)->findOrFail($itemId);
        $this->authorize('delete', $item);

        if ($sale->status !== 'pending') {
            return response()->json([
                'message' => 'Item hanya bisa dihapus jika transaksi masih berstatus pending.',
            ], 422);
        }

        DB::transaction(function () use ($sale, $item) {
            $product  = $item->product;
            $stokBaru = $product->stok + $item->jumlah;

            ModelStockMovements::create([
                'product_id'   => $product->id,
                'tipe'         => 'retur',
                'jumlah'       => $item->jumlah,
                'stok_sebelum' => $product->stok,
                'stok_sesudah' => $stokBaru,
                'referensi'    => $sale->nomor_transaksi,
                'keterangan'   => 'Hapus item dari transaksi ' . $sale->nomor_transaksi,
                'user_id'      => auth()->id(),
            ]);

            $product->update(['stok' => $stokBaru]);
            $item->delete();
            $sale->update(['total' => $sale->salesItems()->sum('subtotal')]);
        });

        return response()->json([
            'message' => 'Item berhasil dihapus.',
        ]);
    }
}
