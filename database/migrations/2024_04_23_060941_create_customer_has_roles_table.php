<?php

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
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
        // جدول ارتباطی بین مشتریان و نقش‌های مشتریان
        Schema::create('customer_has_role', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on('customer_roles')
                ->onDelete('cascade');

            $table->primary(['customer_id', 'role_id']); // تعریف کلید اصلی ترکیبی
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_has_role');
    }
};




