<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGudangStockTable extends Migration
{
    public function up()
    {
        Schema::create('gudang_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stocks_id')->constrained()->onDelete('cascade');
            $table->foreignId('gudangs_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gudang_stock');
    }
}
