<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('nganh', function (Blueprint $table) {
            $table->id('maNganh');
            $table->unsignedBigInteger('maBoMon');
            $table->string('tenNganh', 100);
            $table->string('maSo', 20)->unique();
            $table->timestamps();

            $table->foreign('maBoMon')->references('maBoMon')->on('bomon')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('nganh');
    }
};
