<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customers_id')->constrained()->onDelete('cascade');
            $table->decimal('total', 19, 2)->default(0);
            $table->string('tipe');
            $table->decimal('total_netto', 19, 2)->default(0);
            $table->integer('diskon')->default(0);
            $table->decimal('ppn', 19, 2)->default(0);
            $table->decimal('dpp', 19, 2)->default(0);
            $table->string('status');
            $table->string('sales');
            $table->date('tenggat_waktu');
            $table->foreignId('users_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('penjualan');
    }
};