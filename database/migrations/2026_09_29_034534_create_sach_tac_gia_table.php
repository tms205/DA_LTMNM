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
        Schema::create('sach_tac_gia', function (Blueprint $table) {
            $table->foreignId('sach_id')->constrained('saches')->cascadeOnDelete();
            $table->foreignId('tac_gia_id')->constrained('tac_gias')->cascadeOnDelete();

            $table->primary(['sach_id', 'tac_gia_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sach_tac_gia');
    }
};
