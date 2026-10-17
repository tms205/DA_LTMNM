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
        Schema::table('users', function (Blueprint $table): void {
            $table->string('so_dien_thoai', 20)->nullable()->after('email');
            $table->string('dia_chi', 255)->nullable()->after('so_dien_thoai');
            $table->date('ngay_sinh')->nullable()->after('dia_chi');
            $table->string('google2fa_secret', 64)->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['so_dien_thoai', 'dia_chi', 'ngay_sinh', 'google2fa_secret']);
        });
    }
};
