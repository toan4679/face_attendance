<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bomon', function (Blueprint $table) {
            $table->id('maBoMon');
            $table->unsignedBigInteger('maKhoa');
            $table->string('tenBoMon', 100);
            $table->timestamps();

            $table->foreign('maKhoa')->references('maKhoa')->on('khoa')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('bomon');
    }
};
