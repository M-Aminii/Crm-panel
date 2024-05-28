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
        Schema::create('dimension_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('product_index');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('type_id');
            $table->string('edge_type');
            $table->string('glue_type');
            $table->string('post_type');
            $table->string('delivery_date');
            $table->string('frame');
            $table->string('balance');
            $table->string('vault_type');
            $table->string('part_number');
            $table->string('map_dimension');
            $table->string('map_view');
            $table->string('vault_number');
            $table->string('delivery_meterage');
            $table->string('order_number');
            $table->string('usage');
            $table->string('car_type');
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
        Schema::dropIfExists('dimension_items');
    }
};
