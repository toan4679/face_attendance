<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bomon', function (Blueprint $table) {
            $table->id('bomon_id');
            $table->string('ten_bo_mon', 100);
            $table->foreignId('khoa_id')
                  ->constrained('khoa', 'khoa_id')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
            $table->timestamps();
            $table->unique(['khoa_id','ten_bo_mon']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('bomon');
    }
};
