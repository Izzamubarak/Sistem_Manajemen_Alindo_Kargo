<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('data_pakets', function (Blueprint $table) {
            // Hapus foreign key dulu
            $table->dropForeign(['vendor_id']);
            // Baru hapus kolomnya
            $table->dropColumn('vendor_id');
        });
    }

    public function down()
    {
        Schema::table('data_pakets', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors');
        });
    }
};
