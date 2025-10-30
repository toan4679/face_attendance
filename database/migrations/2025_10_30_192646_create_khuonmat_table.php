<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('khuonmat', function (Blueprint $table) {
            $table->id('maKhuonMat');
            $table->unsignedBigInteger('maSV');
            $table->string('duongDanAnh', 255)->nullable();
            $table->longText('duLieuNhanDien')->nullable();
            $table->timestamps();

            $table->foreign('maSV')->references('maSV')->on('sinhvien')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('khuonmat');
    }
};
