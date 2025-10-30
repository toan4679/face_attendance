<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('faces', function (Blueprint $table) {
            $table->id('face_id');
            $table->foreignId('user_id')
                  ->constrained('users', 'user_id')
                  ->cascadeOnDelete();
            $table->json('embedding');
            $table->string('image_url')->nullable();
            $table->timestamp('captured_at')->useCurrent();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('faces');
    }
};
