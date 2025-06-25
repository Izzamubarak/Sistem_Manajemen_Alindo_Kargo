<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResiToBiayaOperasionalsTable extends Migration
{
    public function up()
    {
        Schema::table('biaya_operasionals', function (Blueprint $table) {
            $table->string('resi')->nullable()->after('created_by');
        });
    }

    public function down()
    {
        Schema::table('biaya_operasionals', function (Blueprint $table) {
            $table->dropColumn('resi');
        });
    }
}
