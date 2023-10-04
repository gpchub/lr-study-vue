<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HocVien extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'hoc_vien';

    protected $fillable = [
        'ho',
        'ten',
        'email',
        'dien_thoai',
        'ngay_sinh',
        'gioi_tinh',
    ];

    protected $casts = [
        'ngay_sinh' => 'datetime'
    ];

    protected $appends = ['ho_va_ten'];

    public function hoVaTen() : Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->ho} {$this->ten}",
        );
    }

    public function lop_hoc() : BelongsToMany
    {
        return $this->belongsToMany(LopHoc::class, 'lop_hoc_vien', 'hoc_vien_id', 'lop_hoc_id')
                    ->using(LopHocVien::class)
                    ->withPivot(['id', 'ngay_bat_dau', 'tinh_trang']);
    }

    public function hoc_phi() : HasMany
    {
        return $this->hasMany(HocPhi::class);
    }
}
