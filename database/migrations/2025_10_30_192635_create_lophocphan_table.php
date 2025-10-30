<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lophocphan', function (Blueprint $table) {
            $table->id('maLopHP');
            $table->unsignedBigInteger('maMon');
            $table->unsignedBigInteger('maGV');
            $table->string('maSoLopHP', 50)->unique();
            $table->string('hocKy', 20);
            $table->string('namHoc', 20);
            $table->text('thongTinLichHoc')->nullable();
            $table->timestamps();

            $table->foreign('maMon')->references('maMon')->on('monhoc')->onDelete('cascade');
            $table->foreign('maGV')->references('maGV')->on('giangvien')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('lophocphan');
    }
};
