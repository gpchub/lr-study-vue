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
        Schema::create('chi_nhanh', function (Blueprint $table) {
            $table->id();
            $table->string('ten', 100);
            $table->string('email', 100);
            $table->string('dien_thoai', 20);
            $table->string('dia_chi', 255);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_nhanh');
    }
};
