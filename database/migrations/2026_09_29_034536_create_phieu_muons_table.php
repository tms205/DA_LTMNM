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
        Schema::create('phieu_muons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sach_id')->constrained('saches')->cascadeOnDelete();
            $table->date('ngay_muon');
            $table->date('ngay_tra_du_kien');
            $table->date('ngay_tra_thuc_te')->nullable();
            $table->enum('trang_thai', ['dang_muon', 'da_tra', 'qua_han'])->default('dang_muon');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_muons');
    }
};
