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
        Schema::create('phieu_phats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phieu_muon_id')->unique()->constrained('phieu_muons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('nguoi_thu_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('so_ngay_qua_han')->default(0);
            $table->unsignedInteger('tien_phat_moi_ngay')->default(5000);
            $table->unsignedInteger('tong_tien_phat')->default(0);
            $table->string('ly_do')->nullable();
            $table->enum('trang_thai', ['chua_nop', 'da_nop'])->default('chua_nop');
            $table->dateTime('ngay_nop')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_phats');
    }
};
