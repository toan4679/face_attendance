<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('phonghoc', function (Blueprint $table) {
            $table->id('phonghoc_id');
            $table->string('ten_phong', 50)->unique();
            $table->unsignedSmallInteger('suc_chua')->default(70);
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('phonghoc');
    }
};
