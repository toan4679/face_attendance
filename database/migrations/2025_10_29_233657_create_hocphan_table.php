<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('hocphan', function (Blueprint $table) {
            $table->id('hocphan_id');
            $table->string('ten_hoc_phan', 150);
            $table->unsignedTinyInteger('so_tin_chi');
            $table->unsignedSmallInteger('so_tiet_LT')->default(0);
            $table->unsignedSmallInteger('so_tiet_TH')->default(0);
            $table->foreignId('bomon_id')
                  ->constrained('bomon', 'bomon_id')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
            $table->timestamps();
            $table->unique(['bomon_id','ten_hoc_phan']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('hocphan');
    }
};
