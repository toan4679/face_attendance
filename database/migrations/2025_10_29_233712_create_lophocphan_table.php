<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lophocphan', function (Blueprint $table) {
            $table->id('lophocphan_id');
            $table->string('ten_LHP', 150);
            $table->unsignedSmallInteger('max_sv')->default(70);
            $table->enum('loai', ['LT','TH']);
            $table->foreignId('hocphan_id')->constrained('hocphan', 'hocphan_id');
            $table->foreignId('giaidoan_id')->constrained('giaidoanhoc', 'giaidoan_id');
            $table->foreignId('giangvien_chinh_id')->constrained('users', 'user_id');
            $table->foreignId('parent_lhp_id')->nullable()
                  ->constrained('lophocphan', 'lophocphan_id')
                  ->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('lophocphan');
    }
};
