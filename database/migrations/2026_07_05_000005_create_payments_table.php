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
        if (Schema::hasTable('payments')) {
            return;
        }

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->index();
            $table->string('payment_method')->index();
            $table->string('status')->default('pending')->index();
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->string('pagarme_payment_link_id')->nullable()->index();
            $table->string('pagarme_checkout_url')->nullable();
            $table->string('pagarme_order_id')->nullable()->index();
            $table->string('pagarme_charge_id')->nullable()->index();
            $table->string('pagarme_transaction_id')->nullable()->index();
            $table->text('pix_qr_code')->nullable();
            $table->timestamp('pix_expires_at')->nullable();
            $table->string('boleto_url')->nullable();
            $table->string('boleto_barcode')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
