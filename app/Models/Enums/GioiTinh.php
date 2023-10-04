<?php
namespace App\Models\Enums;

enum GioiTinh : int
{
    case Nam = 1;
    case Nu = 2;
    case Unknown = 0;

    public function title() : string
    {
        return match($this)
        {
            self::Nam => 'Nam',
            self::Nu => 'Nữ',
            self::Unknown => 'Không rõ'
        };
    }
}