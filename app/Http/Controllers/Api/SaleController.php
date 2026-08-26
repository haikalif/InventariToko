<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleRequest;
use App\Http\Resources\SaleCollection;
use App\Http\Resources\SaleResource;
use App\Models\ModelSales;
use App\Models\ModelStockMovements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $sales = ModelSales::with(['user', 'salesItems.product'])
            ->when($request->search, fn($q) =>
                $q->where('nomor_transaksi', 'like', "%{$request->search}%")
            )
            ->when($request->status, fn($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->metode_pembayaran, fn($q) =>
                $q->where('metode_pembayaran', $request->metode_pembayaran)
            )
            ->when($request->user_id, fn($q) =>
                $q->where('user_id', $request->user_id)
            )
            ->when($request->tanggal_dari, fn($q) =>
                $q->whereDate('tanggal', '>=', $request->tanggal_dari)
            )
            ->when($request->tanggal_sampai, fn($q) =>
                $q->whereDate('tanggal', '<=', $request->tanggal_sampai)
            )
            ->paginate(10);

        return (new SaleCollection($sales))->additional([
            'message' => 'Data penjualan berhasil ditampilkan.',
        ]);
    }

    public function store(SaleRequest $request)
    {
        $sale = ModelSales::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
            'total'   => 0,
        ]);

        return response()->json([
            'message' => 'Transaksi berhasil dibuat.',
            'data'    => new SaleResource($sale->load('user')),
        ], 201);
    }

    public function show(string $id)
    {
        $sale = ModelSales::with(['user', 'salesItems.product'])->findOrFail($id);

        return response()->json([
            'message' => 'Transaksi berhasil ditampilkan.',
            'data'    => new SaleResource($sale),
        ]);
    }

    public function update(SaleRequest $request, string $id)
    {
        $sale = ModelSales::with('salesItems.product')->findOrFail($id);

        // Kalau dibatalkan, kembalikan stok
        if ($request->status === 'dibatalkan' && $sale->status !== 'dibatalkan') {
            DB::transaction(function () use ($sale) {
                foreach ($sale->salesItems as $item) {
                    $product  = $item->product;
                    $stokBaru = $product->stok + $item->jumlah;

                    ModelStockMovements::create([
                        'product_id'   => $product->id,
                        'tipe'         => 'retur',
                        'jumlah'       => $item->jumlah,
                        'stok_sebelum' => $product->stok,
                        'stok_sesudah' => $stokBaru,
                        'referensi'    => $sale->nomor_transaksi,
                        'keterangan'   => 'Retur dari transaksi ' . $sale->nomor_transaksi,
                        'user_id'      => auth()->id(),
                    ]);

                    $product->update(['stok' => $stokBaru]);
                }
            });
        }

        $sale->update($request->validated());

        return response()->json([
            'message' => 'Transaksi berhasil diupdate.',
            'data'    => new SaleResource($sale->load(['user', 'salesItems.product'])),
        ]);
    }

    public function destroy(string $id)
    {
        $sale = ModelSales::findOrFail($id);

        if ($sale->status !== 'pending') {
            return response()->json([
                'message' => 'Hanya transaksi berstatus pending yang dapat dihapus.',
            ], 422);
        }

        $sale->delete();

        return response()->json([
            'message' => 'Transaksi berhasil dihapus.',
        ]);
    }
}
