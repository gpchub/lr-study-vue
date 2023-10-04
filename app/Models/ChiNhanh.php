<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChiNhanh extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'chi_nhanh';

    protected $fillable = [
        'ten',
        'email',
        'dien_thoai',
        'dia_chi',
    ];

    public function lop_hoc(): HasMany
    {
        return $this->hasMany(LopHoc::class);
    }
}
