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
        Schema::create('giao_vien', function (Blueprint $table) {
            $table->id();
            $table->string('ho', 20);
            $table->string('ten', 20)->index();
            $table->string('email', 100);
            $table->string('dien_thoai', 20);
            $table->date('ngay_sinh');
            $table->tinyInteger('gioi_tinh');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giao_vien');
    }
};
