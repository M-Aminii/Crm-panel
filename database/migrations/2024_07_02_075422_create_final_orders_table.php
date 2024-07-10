<?php

use App\Enums\CustomerSendStatus;
use App\Enums\FactorySendStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('final_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('serial_number', 100);
            $table->timestamp('delivery_date')->nullable();
            $table->boolean('sent_to_factory')->default(false);
            $table->boolean('sent_to_customer')->default(false);
            $table->timestamp('informal_invoice_date')->nullable();
            $table->timestamp('formal_invoice_date')->nullable();
            $table->integer('delivery_time')->nullable();
            $table->timestamp('financial_approval_date')->nullable();
            $table->string('pdf_map')->nullable();
            $table->string('cad_map')->nullable();
            $table->string('pdf_dimension')->nullable();
            $table->string('xml_dimension')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();


            $table->foreign('invoice_id')
                ->references('id')
                ->on('invoices')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_orders');
    }
};
