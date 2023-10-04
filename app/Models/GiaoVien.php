<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GiaoVien extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'giao_vien';

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

    public function lop_hoc(): HasMany
    {
        return $this->hasMany(LopHoc::class);
    }
}
