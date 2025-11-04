<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lop', function (Blueprint $table) {
            $table->id('maLop');
            $table->string('maSoLop')->unique();
            $table->string('tenLop');
            $table->unsignedBigInteger('maNganh');
            $table->string('khoaHoc'); // VD: 2021–2025
            $table->string('coVan')->nullable();
            $table->timestamps();

            // Liên kết với bảng nganh
            $table->foreign('maNganh')->references('maNganh')->on('nganh')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lop');
    }
};
