<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('buoihoc', function (Blueprint $table) {
            $table->id('maBuoi');
            $table->unsignedBigInteger('maLopHP');
            $table->unsignedBigInteger('maGV');
            $table->date('ngayHoc');
            $table->time('gioBatDau');
            $table->time('gioKetThuc');
            $table->string('phongHoc', 50)->nullable();
            $table->string('maQR', 255)->nullable();
            $table->timestamps();

            $table->foreign('maLopHP')->references('maLopHP')->on('lophocphan')->onDelete('cascade');
            $table->foreign('maGV')->references('maGV')->on('giangvien')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('buoihoc');
    }
};
