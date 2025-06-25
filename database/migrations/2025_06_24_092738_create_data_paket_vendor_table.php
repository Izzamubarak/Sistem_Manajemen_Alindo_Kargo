<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataPaketVendorTable extends Migration
{
    public function up()
    {
        Schema::create('data_paket_vendor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('data_paket_id');
            $table->unsignedBigInteger('vendor_id');
            $table->decimal('biaya_vendor', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('data_paket_id')->references('id')->on('data_pakets')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('data_paket_vendor');
    }
}
