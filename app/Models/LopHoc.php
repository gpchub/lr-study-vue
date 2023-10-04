<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LopHoc extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lop_hoc';

    public function chi_nhanh(): BelongsTo
    {
        return $this->belongsTo(ChiNhanh::class, 'chi_nhanh_id');
    }

    public function giao_vien(): BelongsTo
    {
        return $this->belongsTo(GiaoVien::class, 'giao_vien_id');
    }

    public function hoc_vien(): BelongsToMany
    {
        return $this->belongsToMany(HocVien::class, 'lop_hoc_vien', 'lop_hoc_id', 'hoc_vien_id')
                    ->using(LopHocVien::class);
    }

    public function hoc_phi() : HasMany
    {
        return $this->hasMany(HocPhi::class);
    }
}
