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
            $table->integer('key');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('type_id');
            $table->integer('height');
            $table->integer('width');
            $table->integer('weight');
            $table->integer('quantity');
            $table->integer('over');
            $table->string('description');
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
