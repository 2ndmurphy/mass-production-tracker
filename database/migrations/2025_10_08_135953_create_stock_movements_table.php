<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->foreignId('raw_batch_id')->nullable()->constrained('raw_material_batches')->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->enum('type', ['in','out','transfer_in','transfer_out']);
            $table->decimal('quantity', 12, 4);
            $table->string('unit', 20)->nullable();
            $table->foreignId('related_production_id')->nullable()->constrained('productions')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->text('note')->nullable();   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
