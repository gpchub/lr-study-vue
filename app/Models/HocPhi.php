<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class HocPhi extends Model
{
    use HasFactory;

    protected $table = 'hoc_phi';

    protected $fillable = [
        'lop_hoc_id',
        'hoc_vien_id',
        'so_tien',
        'thang',
        'nam',
        'ngay_dong',
    ];

    protected $casts = [
        'ngay_dong' => 'date'
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
