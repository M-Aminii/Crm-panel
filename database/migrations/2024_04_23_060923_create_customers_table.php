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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('name', 100)->nullable();
            $table->string('national_id')->unique()->nullable(); // شماره اقتصادی /شناسه ملی
            $table->string('registration_number')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('mobile', 13)->unique()->nullable();
            $table->enum('type', CustomerType::toArray());
            $table->enum('status', CustomerStatus::toArray());
            $table->string('postal_code',10)->unique()->nullable();
            $table->string('address', 255)->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();


            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('province_id')
                ->references('id')
                ->on('provinces')
                ->onDelete('cascade');

            $table->foreign('city_id')
                ->references('id')
                ->on('cities')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};




