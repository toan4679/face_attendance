<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->foreignId('lophocphan_id')->constrained('lophocphan', 'lophocphan_id')->cascadeOnDelete();
            $table->foreignId('sinhvien_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->float('ty_le_nghi', 5, 2)->default(0);
            $table->string('trang_thai_hoc', 30)->default('Dang hoc');
            $table->timestamps();
            $table->primary(['lophocphan_id','sinhvien_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('enrollments');
    }
};
