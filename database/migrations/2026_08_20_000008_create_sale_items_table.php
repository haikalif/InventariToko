<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->string('id', 25)->primary(); // CUID2

            $table->string('sale_id', 25);
            $table->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete();

            $table->string('product_id', 25);
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();

            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
