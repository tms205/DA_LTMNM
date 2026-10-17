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
        Schema::create('tac_gias', function (Blueprint $table) {
            $table->id();
            $table->string('ten_tac_gia', 150);
            $table->integer('nam_sinh')->nullable();
            $table->string('quoc_tich', 100)->nullable();
            $table->text('tieu_su')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tac_gias');
    }
};
