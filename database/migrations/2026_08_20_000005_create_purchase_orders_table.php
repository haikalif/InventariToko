<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->string('id', 25)->primary(); // CUID2
            $table->string('nomor_po')->unique();

            $table->string('supplier_id', 25);
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();

            $table->date('tanggal');
            $table->enum('status', ['draft', 'dipesan', 'diterima_sebagian', 'diterima', 'dibatalkan'])
                ->default('draft');
            $table->decimal('total', 15, 2)->default(0);
            $table->text('catatan')->nullable();

            $table->string('user_id', 25)->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
