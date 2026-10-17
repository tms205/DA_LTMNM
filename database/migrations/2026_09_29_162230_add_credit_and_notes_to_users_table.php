<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('ghi_chu_thu_thu')->nullable()->after('dia_chi');
            $table->boolean('khoa_muon_sach')->default(false)->after('ghi_chu_thu_thu');
            $table->string('ly_do_khoa')->nullable()->after('khoa_muon_sach');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ghi_chu_thu_thu', 'khoa_muon_sach', 'ly_do_khoa']);
        });
    }
};
