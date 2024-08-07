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
            $table->boolean('frame')->nullable();
            $table->boolean('balance')->nullable();
            $table->string('vault_type')->nullable();
            $table->boolean('map_dimension')->nullable();
            $table->boolean('map_view')->nullable();
            $table->string('usage')->nullable();
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
