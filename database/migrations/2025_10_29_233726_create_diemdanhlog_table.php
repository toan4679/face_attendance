<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('diemdanhlog', function (Blueprint $table) {
            $table->id('diemdanh_id');
            $table->foreignId('buoihoc_id')->constrained('buoihockehoach', 'buoihoc_id')->cascadeOnDelete();
            $table->foreignId('sinhvien_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->dateTime('thoi_gian_diem_danh');
            $table->enum('trang_thai', ['Co mat','Vang','Muon'])->default('Co mat');
            $table->float('confidence', 5, 2)->nullable();
            $table->string('image_capture_url')->nullable();
            $table->timestamps();
            $table->unique(['buoihoc_id','sinhvien_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('diemdanhlog');
    }
};
