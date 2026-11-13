<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ModelSales;
use App\Models\ModelStockMovements;
use App\Models\ModelSupliers;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    /**
     * Global search across products, categories, suppliers, sales, purchase orders, stock movements, and users.
     */
    public function index(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        if ($query === '') {
            return response()->json(['results' => []]);
        }

        $like = '%' . $query . '%';
        $role = $request->user()?->role ?? 'staff';
        $canManageInventory = in_array($role, ['superadmin', 'admin', 'staff'], true);
        $canManageUsers = in_array($role, ['superadmin', 'admin'], true);

        $results = [];

        // 1. Produk
        $products = Product::query()
            ->where(function ($q) use ($like) {
                $q->where('nama_produk', 'like', $like)
                    ->orWhere('sku', 'like', $like)
                    ->orWhere('deskripsi', 'like', $like);
            })
            ->limit(5)
            ->get(['id', 'sku', 'nama_produk', 'harga_jual', 'stok', 'satuan']);

        foreach ($products as $item) {
            $results[] = [
                'title' => $item->nama_produk,
                'subtitle' => "SKU: {$item->sku} • Stok: {$item->stok} {$item->satuan}",
                'category' => 'Produk',
                'url' => '/products',
            ];
        }

        // 2. Kategori Produk
        $categories = Category::query()
            ->where('nama_kategori', 'like', $like)
            ->orWhere('deskripsi', 'like', $like)
            ->limit(4)
            ->get(['id', 'nama_kategori', 'deskripsi']);

        foreach ($categories as $item) {
            $results[] = [
                'title' => $item->nama_kategori,
                'subtitle' => $item->deskripsi ?: 'Kategori produk',
                'category' => 'Kategori',
                'url' => '/categories',
            ];
        }

        // 3. Pemasok (Superadmin, Admin, Staff)
        if ($canManageInventory) {
            $suppliers = ModelSupliers::query()
                ->where('nama_supplier', 'like', $like)
                ->orWhere('kontak', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->limit(4)
                ->get(['id', 'nama_supplier', 'kontak', 'email']);

            foreach ($suppliers as $item) {
                $results[] = [
                    'title' => $item->nama_supplier,
                    'subtitle' => trim(($item->kontak ?: '-') . ($item->email ? " • {$item->email}" : '')),
                    'category' => 'Pemasok',
                    'url' => '/suppliers',
                ];
            }

            // 4. Purchase Order
            $orders = PurchaseOrder::query()
                ->where('nomor_po', 'like', $like)
                ->orWhere('status', 'like', $like)
                ->orWhere('catatan', 'like', $like)
                ->limit(4)
                ->get(['id', 'nomor_po', 'status', 'total']);

            foreach ($orders as $item) {
                $results[] = [
                    'title' => $item->nomor_po,
                    'subtitle' => 'Status: ' . strtoupper((string) $item->status) . ' • Rp ' . number_format((float) $item->total, 0, ',', '.'),
                    'category' => 'Purchase Order',
                    'url' => '/purchase-orders',
                ];
            }
        }

        // 5. Penjualan
        $sales = ModelSales::query()
            ->where('nomor_transaksi', 'like', $like)
            ->orWhere('metode_pembayaran', 'like', $like)
            ->orWhere('status', 'like', $like)
            ->limit(4)
            ->get(['id', 'nomor_transaksi', 'metode_pembayaran', 'total']);

        foreach ($sales as $item) {
            $results[] = [
                'title' => $item->nomor_transaksi,
                'subtitle' => strtoupper((string) $item->metode_pembayaran) . ' • Rp ' . number_format((float) $item->total, 0, ',', '.'),
                'category' => 'Penjualan',
                'url' => '/sales',
            ];
        }

        // 6. Mutasi Stok
        $movements = ModelStockMovements::query()
            ->where('referensi', 'like', $like)
            ->orWhere('keterangan', 'like', $like)
            ->orWhere('tipe', 'like', $like)
            ->limit(4)
            ->get(['id', 'tipe', 'jumlah', 'referensi', 'keterangan']);

        foreach ($movements as $item) {
            $results[] = [
                'title' => $item->referensi ?: ('Mutasi #' . $item->id),
                'subtitle' => strtoupper((string) $item->tipe) . " ({$item->jumlah}) • " . ($item->keterangan ?: '-'),
                'category' => 'Mutasi Stok',
                'url' => '/stock-movements',
            ];
        }

        // 7. Karyawan (Superadmin, Admin)
        if ($canManageUsers) {
            $users = User::query()
                ->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('role', 'like', $like)
                ->limit(4)
                ->get(['id', 'name', 'email', 'role']);

            foreach ($users as $item) {
                $results[] = [
                    'title' => $item->name,
                    'subtitle' => "{$item->email} • Role: {$item->role}",
                    'category' => 'Karyawan',
                    'url' => '/users',
                ];
            }
        }

        return response()->json([
            'query' => $query,
            'results' => $results,
        ]);
    }
}
