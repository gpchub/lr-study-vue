<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChungChi extends Model
{
    use HasFactory;

    protected $table = 'chung_chi';



    public function lich_thi() : HasMany
    {
        return $this->hasMany(LichThi::class);
    }
}
