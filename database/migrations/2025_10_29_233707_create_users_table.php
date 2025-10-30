<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('email', 120)->unique();
            $table->string('password');
            $table->string('ho_ten', 100);
            $table->enum('role', ['ADMIN','PDT','GV','SV','BGH']);
            $table->foreignId('bomon_id')->nullable()
                  ->constrained('bomon', 'bomon_id')
                  ->cascadeOnUpdate()
                  ->nullOnDelete();
            $table->foreignId('lophanhchinh_id')->nullable()
                  ->constrained('lophanhchinh', 'lophanhchinh_id')
                  ->cascadeOnUpdate()
                  ->nullOnDelete();
            $table->string('ma_sv', 20)->nullable()->unique();
            $table->rememberToken();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('users');
    }
};
