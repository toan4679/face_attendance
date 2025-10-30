<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lophanhchinh', function (Blueprint $table) {
            $table->id('lophanhchinh_id');
            $table->string('ten_lop', 50)->index();
            $table->foreignId('nganh_id')
                  ->constrained('nganhdaotao', 'nganh_id')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
            $table->timestamps();
            $table->unique(['nganh_id','ten_lop']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('lophanhchinh');
    }
};
