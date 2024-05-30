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
        Schema::create('technical_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('type_id');
            $table->string('edge_type')->nullable();
            $table->string('glue_type')->nullable();
            $table->string('post_type')->nullable();
            $table->string('delivery_date')->nullable();
            $table->string('frame')->nullable();
            $table->string('balance')->nullable();
            $table->string('vault_type')->nullable();
            $table->string('part_number')->nullable();
            $table->string('map_dimension')->nullable();
            $table->string('map_view')->nullable();
            $table->string('vault_number')->nullable();
            $table->string('delivery_meterage')->nullable();
            $table->string('order_number')->nullable();
            $table->string('usage')->nullable();
            $table->string('car_type')->nullable();
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
        Schema::dropIfExists('technical_items');
    }
};
