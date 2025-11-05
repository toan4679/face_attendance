<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('buoihoc', function (Blueprint $table) {
            $table->time('gioBatDau')->nullable()->change();
            $table->time('gioKetThuc')->nullable()->change();
            $table->date('ngayHoc')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('buoihoc', function (Blueprint $table) {
            $table->time('gioBatDau')->nullable(false)->change();
            $table->time('gioKetThuc')->nullable(false)->change();
            $table->date('ngayHoc')->nullable(false)->change();
        });
    }
};
