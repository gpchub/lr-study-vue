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
        Schema::create('chung_chi', function (Blueprint $table) {
            $table->id();
            $table->string('ten', 50);
            $table->text('mo_ta');
            $table->timestamps();
        });

        Schema::create('lich_thi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chung_chi_id');
            $table->timestamp('ngay_thi');
            $table->string('dia_diem');
            $table->timestamps();

            $table->foreign('chung_chi_id')->references('id')->on('chung_chi');
        });

        Schema::create('lich_thi_hoc_vien', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hoc_vien_id');
            $table->unsignedBigInteger('lich_thi_id');
            $table->tinyInteger('tinh_trang')->nullable();
            $table->tinyInteger('ket_qua')->nullable();
            $table->timestamps();

            $table->unique(['hoc_vien_id', 'lich_thi_id']);
            $table->foreign('hoc_vien_id')->references('id')->on('hoc_vien');
            $table->foreign('lich_thi_id')->references('id')->on('lich_thi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_thi_hoc_vien');
        Schema::dropIfExists('lich_thi');
        Schema::dropIfExists('chung_chi');
    }
};
