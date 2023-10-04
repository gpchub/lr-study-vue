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
        Schema::create('lop_hoc', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chi_nhanh_id');
            $table->unsignedBigInteger('giao_vien_id');
            $table->string('ten', 50)->index();
            $table->string('ca_hoc', 50);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('chi_nhanh_id')->references('id')->on('chi_nhanh');
            $table->foreign('giao_vien_id')->references('id')->on('giao_vien');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lop_hoc');
    }
};
