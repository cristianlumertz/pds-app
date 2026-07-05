<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('addresses', 'complement')) {
            return;
        }

        if ($this->isMysqlFamily()) {
            DB::statement('ALTER TABLE addresses MODIFY complement VARCHAR(255) NULL');

            return;
        }

        Schema::table('addresses', function (Blueprint $table) {
            $table->string('complement')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('addresses', 'complement')) {
            return;
        }

        DB::table('addresses')
            ->whereNull('complement')
            ->update(['complement' => '']);

        if ($this->isMysqlFamily()) {
            DB::statement("ALTER TABLE addresses MODIFY complement VARCHAR(255) NOT NULL DEFAULT ''");

            return;
        }

        Schema::table('addresses', function (Blueprint $table) {
            $table->string('complement')->default('')->nullable(false)->change();
        });
    }

    private function isMysqlFamily(): bool
    {
        return in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true);
    }
};
