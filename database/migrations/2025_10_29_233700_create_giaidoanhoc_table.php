<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('giaidoanhoc', function (Blueprint $table) {
            $table->id('giaidoan_id');
            $table->string('ten_giai_doan', 120);
            $table->date('ngay_bat_dau');
            $table->date('ngay_ket_thuc');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('giaidoanhoc');
    }
};
