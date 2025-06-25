<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('data_pakets', function (Blueprint $table) {
            $table->unsignedBigInteger('biaya_operasional_id')->nullable()->after('vendor_id');
            // $table->string('resi')->after('cost');
        });
    }

    public function down(): void
    {
        Schema::table('data_pakets', function (Blueprint $table) {
            $table->dropColumn(['biaya_operasional_id']);
        });
    }
};
