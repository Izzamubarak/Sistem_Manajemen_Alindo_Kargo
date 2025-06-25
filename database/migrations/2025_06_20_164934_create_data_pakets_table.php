<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('data_pakets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->string('resi');
            $table->string('description');
            $table->string('kota_asal');
            $table->string('kota_tujuan');
            $table->decimal('weight', 8, 2);
            $table->decimal('volume', 8, 2);
            $table->integer('jumlah_koli');
            $table->decimal('cost', 12, 2);
            $table->unsignedBigInteger('created_by');
            $table->string('no_hp_pengirim');
            $table->string('penerima');
            $table->string('no_hp_penerima');
            $table->text('alamat_penerima');
            $table->string('status')->default('Dalam Proses');
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_pakets');
    }
};
