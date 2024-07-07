<?php

use App\Enums\InformalInvoiceStatus;
use App\Enums\InvoiceDelivery;
use App\Enums\InvoiceStatus;
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
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial_number', 100);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('description', 100);
            $table->enum('status', InvoiceStatus::toArray());
            $table->enum('informal_status', InformalInvoiceStatus::toArray());
            $table->enum('delivery',InvoiceDelivery::toArray());
            $table->integer('discount')->nullable();
            $table->integer('pre_payment')->nullable();
            $table->integer('before_delivery')->nullable();
            $table->integer('cheque')->nullable();
            $table->bigInteger('amount_payable')->nullable();    // خالی میخوره به این دلیل که در اخر سر برای اپدیت این اضافه میشه
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

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
        Schema::dropIfExists('invoice');
    }
};
