<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LopHocVien extends Pivot
{
    use HasFactory;

    protected $table = 'lop_hoc_vien';

    public $incrementing = true;

    protected $casts = [
        'ngay_bat_dau' => 'date'
    ];

    public function lop_hoc() : BelongsTo
    {
        return $this->belongsTo(LopHoc::class);
    }

    public function hoc_vien() : BelongsTo
    {
        return $this->belongsTo(HocVien::class);
    }
}
