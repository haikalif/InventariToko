<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('id', 25)->primary(); // CUID2
            $table->string('sku')->unique();
            $table->string('nama_produk');

            $table->string('category_id', 25)->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();

            $table->string('supplier_id', 25)->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();

            $table->text('deskripsi')->nullable();
            $table->string('satuan')->default('pcs'); // pcs, kg, box, dll
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->string('barcode')->nullable();
            $table->string('gambar')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->index('nama_produk');
            $table->index('barcode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
