<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('biaya_operasionals', function (Blueprint $table) {
            // Cek dan hapus kolom hanya jika ada
            if (Schema::hasColumn('biaya_operasionals', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('biaya_operasionals', 'amount')) {
                $table->dropColumn('amount');
            }
            if (Schema::hasColumn('biaya_operasionals', 'date')) {
                $table->dropColumn('date');
            }

            // Tambahkan kolom baru hanya jika belum ada
            if (!Schema::hasColumn('biaya_operasionals', 'vendor_info')) {
                $table->json('vendor_info')->nullable();
            }
            if (!Schema::hasColumn('biaya_operasionals', 'total_vendor')) {
                $table->decimal('total_vendor', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('biaya_operasionals', 'total_paket')) {
                $table->decimal('total_paket', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('biaya_operasionals', 'biaya_lainnya')) {
                $table->decimal('biaya_lainnya', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('biaya_operasionals', 'total_keseluruhan')) {
                $table->decimal('total_keseluruhan', 12, 2)->nullable();
            }
        });
    }



    public function down(): void
    {
        Schema::table('biaya_operasionals', function (Blueprint $table) {
            if (Schema::hasColumn('biaya_operasionals', 'vendor_info')) {
                $table->dropColumn('vendor_info');
            }
            if (Schema::hasColumn('biaya_operasionals', 'total_vendor')) {
                $table->dropColumn('total_vendor');
            }
            if (Schema::hasColumn('biaya_operasionals', 'total_paket')) {
                $table->dropColumn('total_paket');
            }
            if (Schema::hasColumn('biaya_operasionals', 'biaya_lainnya')) {
                $table->dropColumn('biaya_lainnya');
            }
            if (Schema::hasColumn('biaya_operasionals', 'total_keseluruhan')) {
                $table->dropColumn('total_keseluruhan');
            }

            // Tambahkan kembali kolom lama hanya jika belum ada
            if (!Schema::hasColumn('biaya_operasionals', 'description')) {
                $table->string('description')->nullable();
            }
            if (!Schema::hasColumn('biaya_operasionals', 'amount')) {
                $table->integer('amount')->default(0);
            }
            if (!Schema::hasColumn('biaya_operasionals', 'date')) {
                $table->date('date')->nullable();
            }
        });
    }
};
