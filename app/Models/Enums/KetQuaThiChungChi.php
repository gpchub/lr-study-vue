<?php
namespace App\Models\Enums;

enum KetQuaThiChungChi : int
{
    case ChuaCo = 0;
    case Dat = 1;
    case KhongDat = 2;

    public function title() : string
    {
        return match($this)
        {
            self::KhongDat => 'Không đạt',
            self::Dat => 'Đạt',
            self::ChuaCo => 'Chưa có',
        };
    }
}