<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lophocphan', function (Blueprint $table) {
            $table->date('ngayBatDau')->nullable()->after('namHoc');
            $table->date('ngayKetThuc')->nullable()->after('ngayBatDau');
        });
    }

    public function down(): void
    {
        Schema::table('lophocphan', function (Blueprint $table) {
            $table->dropColumn(['ngayBatDau', 'ngayKetThuc']);
        });
    }
};
