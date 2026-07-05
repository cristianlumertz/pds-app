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
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('product_id');
            }

            if (! Schema::hasColumn('order_items', 'product_sku')) {
                $table->string('product_sku')->nullable()->after('product_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'product_sku')) {
                $table->dropColumn('product_sku');
            }

            if (Schema::hasColumn('order_items', 'product_name')) {
                $table->dropColumn('product_name');
            }
        });
    }
};
