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
        if (Schema::hasTable('order_coupons')) {
            return;
        }

        Schema::create('order_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->index();
            $table->foreignId('coupon_id')->index();
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['order_id', 'coupon_id']);

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->cascadeOnDelete();

            $table->foreign('coupon_id')
                ->references('id')
                ->on('coupons')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_coupons');
    }
};
