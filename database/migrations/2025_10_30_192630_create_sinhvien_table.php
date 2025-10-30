<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sinhvien', function (Blueprint $table) {
            $table->id('maSV');
            $table->unsignedBigInteger('maNganh');
            $table->string('maLopHanhChinh', 20)->nullable();
            $table->string('maSo', 20)->unique();
            $table->string('hoTen', 100);
            $table->string('email', 100)->unique();
            $table->string('matKhau', 255);
            $table->string('soDienThoai', 20)->nullable();
            $table->integer('khoaHoc')->nullable();
            $table->string('anhDaiDien', 255)->nullable();
            $table->timestamps();

            $table->foreign('maNganh')->references('maNganh')->on('nganh')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('sinhvien');
    }
};
