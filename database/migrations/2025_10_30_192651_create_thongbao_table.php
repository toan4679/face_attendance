<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('thongbao', function (Blueprint $table) {
            $table->id('maThongBao');
            $table->string('tieuDe', 100);
            $table->text('noiDung');
            $table->string('nguoiGui', 100);
            $table->enum('nguoiNhanLoai', ['SinhVien', 'GiangVien']);
            $table->unsignedBigInteger('maNguoiNhan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('thongbao');
    }
};
