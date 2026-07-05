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
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'subtotal_amount')) {
                $table->decimal('subtotal_amount', 10, 2)->default(0.00)->after('status');
            }

            if (! Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0.00)->after('subtotal_amount');
            }

            if (! Schema::hasColumn('orders', 'shipping_amount')) {
                $table->decimal('shipping_amount', 10, 2)->default(0.00)->after('discount_amount');
            }

            if (! Schema::hasColumn('orders', 'shipping_method')) {
                $table->string('shipping_method')->nullable()->after('total_amount');
            }

            if (! Schema::hasColumn('orders', 'shipping_status')) {
                $table->string('shipping_status')->default('pending')->after('shipping_method');
            }

            if (! Schema::hasColumn('orders', 'delivery_estimate')) {
                $table->string('delivery_estimate')->nullable()->after('shipping_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            foreach ([
                'delivery_estimate',
                'shipping_status',
                'shipping_method',
                'shipping_amount',
                'discount_amount',
                'subtotal_amount',
            ] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
