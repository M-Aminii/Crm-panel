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
        Schema::create('dimension_description_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dimension_id');
            $table->unsignedBigInteger('description_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();



            $table->foreign('dimension_id')
                ->references('id')
                ->on('dimension_items')
                ->onDelete('cascade');

            $table->foreign('description_id')
                ->references('id')
                ->on('description_dimensions')
                ->onDelete('cascade');



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dimension_item_description');
    }
};
