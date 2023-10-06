<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LichThiHocVien extends Model
{
    use HasFactory;

    protected $table = 'lich_thi_hoc_vien';
    public $incrementing = true;

    protected $fillable = [
        'lich_thi_id',
        'hoc_vien_id',
        'tinh_trang',
        'ket_qua',
    ];

    public function lich_thi() : BelongsTo
    {
        return $this->belongsTo(LichThi::class);
    }

    public function hoc_vien() : BelongsTo
    {
        return $this->belongsTo(HocVien::class);
    }
}
