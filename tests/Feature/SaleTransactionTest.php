<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ModelSales;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => 'rahasia123',
            'role' => 'superadmin',
        ]);
    }

    public function test_add_item_to_sale_deducts_product_stock()
    {
        $this->actingAs($this->superadmin);

        // Buat kategori
        $category = Category::create([
            'nama_kategori' => 'Minuman',
            'deskripsi' => 'Kategori minuman'
        ]);

        // Buat produk dengan stok awal 50
        $product = Product::create([
            'nama_produk' => 'Kopi Kapal Api',
            'sku' => 'KPA-001',
            'category_id' => $category->id,
            'satuan' => 'pcs',
            'stok' => 50,
            'stok_minimum' => 10,
            'harga_beli' => 1000,
            'harga_jual' => 1500,
            'aktif' => true,
        ]);

        // Buat Transaksi Penjualan (Pending)
        $sale = ModelSales::create([
            'nomor_transaksi' => 'TRX-TEST-01',
            'tanggal' => now(),
            'user_id' => $this->superadmin->id,
            'status' => 'pending',
            'metode_pembayaran' => 'cash',
            'total' => 0
        ]);

        // Hit API Endpoint untuk tambah item
        $response = $this->postJson("/api/sales/{$sale->id}/items", [
            'product_id' => $product->id,
            'jumlah' => 5,
            'harga_satuan' => 1500
        ]);

        // Pastikan sukses
        $response->assertStatus(201);

        // Pastikan stok berkurang
        $product->refresh();
        $this->assertEquals(45, $product->stok);

        // Pastikan total transaksi terupdate
        $sale->refresh();
        $this->assertEquals(7500, $sale->total); // 5 * 1500

        // Pastikan log stok movement tercatat
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'tipe' => 'keluar',
            'jumlah' => 5,
            'stok_sebelum' => 50,
            'stok_sesudah' => 45,
            'referensi' => 'TRX-TEST-01'
        ]);
    }

    public function test_cannot_add_item_if_stock_insufficient()
    {
        $this->actingAs($this->superadmin);

        $category = Category::create([
            'nama_kategori' => 'Minuman',
        ]);

        // Stok cuma 10
        $product = Product::create([
            'nama_produk' => 'Kopi Kapal Api',
            'sku' => 'KPA-002',
            'category_id' => $category->id,
            'satuan' => 'pcs',
            'stok' => 10,
            'stok_minimum' => 10,
            'harga_beli' => 1000,
            'harga_jual' => 1500,
        ]);

        $sale = ModelSales::create([
            'nomor_transaksi' => 'TRX-TEST-02',
            'tanggal' => now(),
            'user_id' => $this->superadmin->id,
            'status' => 'pending',
            'metode_pembayaran' => 'cash',
            'total' => 0
        ]);

        // Coba beli 15
        $response = $this->postJson("/api/sales/{$sale->id}/items", [
            'product_id' => $product->id,
            'jumlah' => 15,
            'harga_satuan' => 1500
        ]);

        // Harus gagal (422 Unprocessable Entity)
        $response->assertStatus(422);

        // Stok tidak boleh berkurang
        $product->refresh();
        $this->assertEquals(10, $product->stok);
    }
}
