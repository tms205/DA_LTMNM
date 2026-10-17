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
        Schema::create('saches', function (Blueprint $table) {
            $table->id();
            $table->string('ten_sach', 200);
            $table->foreignId('the_loai_id')->constrained('the_loais')->cascadeOnDelete();
            $table->integer('nam_xuat_ban')->nullable();
            $table->string('nha_xuat_ban', 150)->nullable();
            $table->integer('so_luong')->default(0);
            $table->string('hinh_anh')->nullable();
            $table->text('mo_ta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saches');
    }
};
