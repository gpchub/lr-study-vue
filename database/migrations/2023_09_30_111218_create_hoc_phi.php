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
        Schema::create('hoc_phi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hoc_vien_id');
            $table->unsignedBigInteger('lop_hoc_id');
            $table->decimal('so_tien', 10, 0);
            $table->unsignedTinyInteger('thang');
            $table->unsignedInteger('nam');
            $table->dateTime('ngay_dong');
            $table->timestamps();

            $table->unique(['hoc_vien_id', 'lop_hoc_id', 'thang', 'nam']);
            $table->foreign('hoc_vien_id')->references('id')->on('hoc_vien');
            $table->foreign('lop_hoc_id')->references('id')->on('lop_hoc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoc_phi');
    }
};
