<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('buoihoc', function (Blueprint $table) {
        $table->string('thu', 20)->nullable()->after('maGV');          // ví dụ: "Thứ 2"
        $table->integer('tietBatDau')->nullable()->after('thu');       // Tiết bắt đầu (vd: 1)
        $table->integer('tietKetThuc')->nullable()->after('tietBatDau'); // Tiết kết thúc (vd: 3)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buoihoc', function (Blueprint $table) {
            //
        });
    }
};
