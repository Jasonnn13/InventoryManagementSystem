<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suppliers_id')->constrained()->onDelete('cascade');
            $table->decimal('total', 19, 2)->default(0);
            $table->foreignId('users_id')->constrained()->onDelete('cascade');
            $table->foreignId('gudangs_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembelian');
    }
};