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
        Schema::create('final_order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('key');
            $table->unsignedBigInteger('final_order_id');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('area', 10, 3); // متراژ
            $table->timestamps();

            $table->foreign('final_order_id')
                ->references('id')
                ->on('final_orders')
                ->onDelete('cascade');

            $table->foreign('type_id')
                ->references('id')
                ->on('type_items')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_order_items');
    }
};
