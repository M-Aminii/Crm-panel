<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFinalOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('final_order_items', function (Blueprint $table) {
            $table->string('delivery_date')->nullable()->after('type_id');
        });
    }

    public function down()
    {
        Schema::table('final_order_items', function (Blueprint $table) {
            $table->dropColumn('delivery_date');
        });
    }

}


