<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('khoa', function (Blueprint $table) {
            $table->id('maKhoa');
            $table->string('tenKhoa', 100);
            $table->text('moTa')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('khoa');
    }
};
