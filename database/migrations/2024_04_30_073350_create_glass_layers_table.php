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
        Schema::create('glass_layers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->integer('layer_number');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('width_id');
            $table->unsignedBigInteger('material_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->foreign('type_id')
                ->references('id')
                ->on('glass_types')
                ->onDelete('cascade');

            $table->foreign('width_id')
                ->references('id')
                ->on('glass_widths')
                ->onDelete('cascade');

            $table->foreign('material_id')
                ->references('id')
                ->on('glass_materials')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('glass_layer');
    }
};
