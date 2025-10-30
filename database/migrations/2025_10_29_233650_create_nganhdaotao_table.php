<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('nganhdaotao', function (Blueprint $table) {
            $table->id('nganh_id');
            $table->string('ten_nganh', 100);
            $table->foreignId('bomon_id')
                  ->constrained('bomon', 'bomon_id')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
            $table->timestamps();
            $table->unique(['bomon_id','ten_nganh']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('nganhdaotao');
    }
};
