<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('lophocphan', function (Blueprint $table) {
            $table->json('dsMaLop')->nullable()->after('maGV');
        });
    }

    public function down()
    {
        Schema::table('lophocphan', function (Blueprint $table) {
            $table->dropColumn('dsMaLop');
        });
    }
};
