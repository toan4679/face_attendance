<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('giangvien', function (Blueprint $table) {
            $table->id('maGV');
            $table->unsignedBigInteger('maBoMon');
            $table->string('hoTen', 100);
            $table->string('email', 100)->unique();
            $table->string('matKhau', 255);
            $table->string('soDienThoai', 20)->nullable();
            $table->string('hocVi', 50)->nullable();
            $table->timestamps();

            $table->foreign('maBoMon')->references('maBoMon')->on('bomon')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('giangvien');
    }
};
