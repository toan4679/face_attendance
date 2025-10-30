<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('monhoc', function (Blueprint $table) {
            $table->id('maMon');
            $table->unsignedBigInteger('maNganh');
            $table->string('maSoMon', 20)->unique();
            $table->string('tenMon', 100);
            $table->integer('soTinChi');
            $table->text('moTa')->nullable();
            $table->timestamps();

            $table->foreign('maNganh')->references('maNganh')->on('nganh')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('monhoc');
    }
};
