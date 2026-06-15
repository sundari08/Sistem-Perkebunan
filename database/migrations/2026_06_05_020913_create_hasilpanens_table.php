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
        Schema::create('hasilpanens', function (Blueprint $table) {
            $table->id();
            $table->date('tgl');
            $table->string('estate');
            $table->string('divisi');
            $table->string('blok');
            $table->string('mandor');
            $table->string('kerani');
            $table->integer('tph');
            $table->string('pemanen');
            $table->integer('janjang');
            $table->integer('matang');
            $table->integer('mentah');
            $table->integer('kurangmatang');
            $table->integer('lewatmatang');
            $table->integer('partenorcarpi');
            $table->integer('buahbatu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasilpanens');
    }
};
