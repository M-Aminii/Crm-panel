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
        Schema::create('aggregated_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('key');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('type_id');
            $table->text('description_product');
            $table->decimal('total_area', 10, 3);
            $table->integer('total_quantity');
            $table->integer('total_weight');
            $table->integer('price_unit');
            $table->integer('price_discounted');
            $table->integer('value_added_tax');
            $table->bigInteger('total_price');
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('invoice_id')
                ->references('id')
                ->on('invoices')
                ->onDelete('cascade');

            $table->foreign('type_id')
                ->references('id')
                ->on('type_items')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aggregated_items');
    }
};
