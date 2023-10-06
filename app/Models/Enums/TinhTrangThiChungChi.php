<?php
namespace App\Models\Enums;

enum TinhTrangThiChungChi : int
{
    case ChuaThi = 0;
    case DaTinh = 1;

    public function title() : string
    {
        return match($this)
        {
            self::ChuaThi => 'Chưa thi',
            self::DaTinh => 'Đã thi',
        };
    }
}