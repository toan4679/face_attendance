<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('diemdanh', function (Blueprint $table) {
            $table->id('maDiemDanh');
            $table->unsignedBigInteger('maBuoi');
            $table->unsignedBigInteger('maSV');
            $table->enum('trangThai', ['Có mặt', 'Vắng', 'Đi muộn'])->default('Vắng');
            $table->dateTime('thoiGianDiemDanh')->nullable();
            $table->enum('hinhThuc', ['Khuôn mặt', 'QR'])->nullable();
            $table->boolean('xacThucKhuonMat')->default(false);
            $table->timestamps();

            $table->foreign('maBuoi')->references('maBuoi')->on('buoihoc')->onDelete('cascade');
            $table->foreign('maSV')->references('maSV')->on('sinhvien')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('diemdanh');
    }
};
