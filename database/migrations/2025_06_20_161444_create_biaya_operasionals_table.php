<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('biaya_operasionals', function (Blueprint $table) {
            $table->id();
            $table->string('description');         
            $table->integer('amount');            
            $table->date('date');                  
            $table->foreignId('created_by')       
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamps();                  
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_operasionals');
    }
};
