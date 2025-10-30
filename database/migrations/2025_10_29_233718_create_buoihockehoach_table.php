<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('buoihockehoach', function (Blueprint $table) {
            $table->id('buoihoc_id');
            $table->foreignId('lophocphan_id')->constrained('lophocphan', 'lophocphan_id');
            $table->foreignId('phonghoc_id')->constrained('phonghoc', 'phonghoc_id');
            $table->dateTime('thoi_gian_bat_dau');
            $table->unsignedTinyInteger('so_tiet')->default(2);
            $table->enum('trang_thai', ['Chua dien ra','Da dien ra','Da huy'])->default('Chua dien ra');
            $table->text('noi_dung_giang_day')->nullable();
            $table->foreignId('giangvien_day_id')->constrained('users', 'user_id');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('buoihockehoach');
    }
};
