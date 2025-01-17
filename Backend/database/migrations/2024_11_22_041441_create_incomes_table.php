<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomesTable extends Migration
{
    public function up()
{
    Schema::create('incomes', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->string('source');
        $table->bigInteger('amount');
        $table->date('date');
        $table->text('description')->nullable();
        $table->foreignId('category_id')->constrained('categories'); // Mối quan hệ với bảng categories
        $table->timestamps();
    });
}


    public function down()
    {
        Schema::dropIfExists('incomes');
    }
}
