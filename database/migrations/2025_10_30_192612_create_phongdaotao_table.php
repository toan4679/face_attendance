<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('phongdaotao', function (Blueprint $table) {
            $table->id('maPDT');
            $table->unsignedBigInteger('maAdmin');
            $table->string('hoTen', 100);
            $table->string('email', 100)->unique();
            $table->string('matKhau', 255);
            $table->string('soDienThoai', 20)->nullable();
            $table->timestamps();

            $table->foreign('maAdmin')->references('maAdmin')->on('admin')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('phongdaotao');
    }
};
