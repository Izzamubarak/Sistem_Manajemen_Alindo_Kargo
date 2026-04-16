<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update super-admin to superadmin
        DB::table('users')->where('role', 'super-admin')->update(['role' => 'superadmin']);
        
        // Update tim-operasional and operasional to admin
        DB::table('users')->whereIn('role', ['tim-operasional', 'operasional'])->update(['role' => 'admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible deterministically since tim-operasional and operasional are merged to admin.
    }
};
