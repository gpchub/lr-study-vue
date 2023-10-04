<?php
namespace App\Models\Enums;

enum TinhTrangHocVien : int
{
    case DangHoc = 1;
    case DaNghi = 2;
    case Unknown = 0;

    public function title() : string
    {
        return match($this)
        {
            self::DangHoc => 'Đang học',
            self::DaNghi => 'Đã nghỉ',
            self::Unknown => 'Không rõ'
        };
    }
}