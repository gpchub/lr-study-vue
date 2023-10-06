<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LichThi extends Model
{
    use HasFactory;

    protected $table = 'lich_thi';

    protected $casts = [
        'ngay_thi' => 'datetime'
    ];

    public function chung_chi() : BelongsTo
    {
        return $this->belongsTo(ChungChi::class);
    }

    public function hoc_vien() : BelongsToMany
    {
        return $this->belongsToMany(
                        HocVien::class,
                        'lich_thi_hoc_vien',
                        'lich_thi_id',
                        'hoc_vien_id',
                    )->withPivot(['id', 'tinh_trang', 'ket_qua']);
    }
}
