<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah kolom biaya_lainnya menjadi JSON.
     */
    public function up(): void
    {
        Schema::table('biaya_operasionals', function (Blueprint $table) {
            $table->json('biaya_lainnya')->nullable()->change();
        });
    }

    /**
     * Kembalikan ke bentuk sebelumnya (misal decimal) jika rollback.
     */
    public function down(): void
    {
        Schema::table('biaya_operasionals', function (Blueprint $table) {
            $table->decimal('biaya_lainnya', 12, 2)->nullable()->change();
        });
    }
};
