<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('dangkyhoc', function (Blueprint $table) {
            $table->id('maDangKy');
            $table->unsignedBigInteger('maLopHP');
            $table->unsignedBigInteger('maSV');
            $table->timestamps();

            $table->foreign('maLopHP')->references('maLopHP')->on('lophocphan')->onDelete('cascade');
            $table->foreign('maSV')->references('maSV')->on('sinhvien')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('dangkyhoc');
    }
};
