<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ma_qrcode', function (Blueprint $table) {
            $table->id('qrcode_id');
            $table->uuid('code_value')->unique();
            $table->dateTime('thoi_gian_tao');
            $table->dateTime('han_su_dung');
            $table->foreignId('buoihoc_id')->constrained('buoihockehoach', 'buoihoc_id')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('ma_qrcode');
    }
};
