<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('user_id')->notNull();;
            $table->bigInteger('total_income')->nullable();
            $table->bigInteger('total_expense')->nullable();
            $table->bigInteger('balance')->nullable();
            $table->date('date')->notNull();
           
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};
