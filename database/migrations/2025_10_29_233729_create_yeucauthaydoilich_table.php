<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('yeucauthaydoilich', function (Blueprint $table) {
            $table->id('yeucau_id');
            $table->enum('loai_yeu_cau', ['Nghi','Day bu','Day thay']);
            $table->text('ly_do');
            $table->enum('trang_thai_duyet', ['Cho PDT','Da duyet','Tu choi'])->default('Cho PDT');
            $table->foreignId('nguoi_yeu_cau_id')->constrained('users', 'user_id');
            $table->foreignId('nguoi_day_thay_id')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->foreignId('buoihoc_goc_id')->constrained('buoihockehoach', 'buoihoc_id');
            $table->foreignId('buoihoc_bu_id')->nullable()->constrained('buoihockehoach', 'buoihoc_id')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('yeucauthaydoilich');
    }
};
