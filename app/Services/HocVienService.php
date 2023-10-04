<?php

namespace App\Services;

use App\Models\HocVien;
use App\Models\LopHoc;
use App\Models\LopHocVien;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class HocVienService
{
    public function getAll($data = [])
    {
        $sort = isset($data['sort']) ? $data['sort'] : '';
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        $lop = $data['lop'] ?? '';
        $pageSize = $data['page-size'] ?? 10;

        $query = HocVien::query()->with('lop_hoc');

        if (!empty($lop)) {

            $query->whereHas('lop_hoc', function ($builder) use ($lop) {
                $builder->where('id', $lop);
            });

            $ngay_bat_dau = $data['ngay_bat_dau'] ?? '';
            $ngay_ket_thuc = $data['ngay_ket_thuc'] ?? '';

            if (!empty($ngay_bat_dau)) {
                //tim tu ngay bd ve sau
                $query->wherePivot('created_at', '>=', $ngay_bat_dau);
            }

            if (!empty($ngay_ket_thuc)) {
                $dt = Carbon::createFromFormat('Y-m-d', $ngay_ket_thuc);
                $query->wherePivot('created_at', '<=', $dt->addDay());
            }
        }

        if (!empty($name)) {
            $query->where('name', 'like', '%' . $name . '%');
        }
        if (!empty($email)) {
            $query->where('email', 'like', '%' . $email . '%');
        }
        if (!empty($phone)) {
            $query->where('phone', 'like', '%' . $phone . '%');
        }

        if ($sort == 'name') {
            $query->orderBy('name');
        } elseif ($sort == 'date') {
            $query->orderByPivot('created_at');
        } else {
            $query->orderBy('id', 'desc');
        }

        $list = $query->paginate($pageSize);

        return $list;
    }

    public function getById($id)
    {
        return HocVien::find($id);
    }

    public function create($data = [])
    {
        $ho = $data['ho'] ?? '';
        $ten = $data['ten'] ?? '';
        $email = $data['email'] ?? '';
        $dien_thoai = $data['dien_thoai'] ?? '';
        $gioi_tinh = $data['gioi_tinh'] ?? 0;
        $ngay_sinh = $data['ngay_sinh'] ?? '';
        $tinh_trang = $data['tinh_trang'] ?? 1;

        $item = new HocVien();
        $item->ho = $ho;
        $item->ten = $ten;
        $item->email = $email;
        $item->dien_thoai = $dien_thoai;
        $item->gioi_tinh = $gioi_tinh;
        $item->ngay_sinh = $ngay_sinh;
        $item->tinh_trang = $tinh_trang;
        $item->save();

        return $item;
    }

    public function update($data = [])
    {
        $id = $data['id'] ?? '';
        $ho = $data['ho'] ?? '';
        $ten = $data['ten'] ?? '';
        $email = $data['email'] ?? '';
        $dien_thoai = $data['dien_thoai'] ?? '';
        $gioi_tinh = $data['gioi_tinh'] ?? 0;
        $ngay_sinh = $data['ngay_sinh'] ?? '';
        $tinh_trang = $data['tinh_trang'] ?? null;

        $item = $this->getById($id);
        $item->ho = $ho;
        $item->ten = $ten;
        $item->email = $email;
        $item->dien_thoai = $dien_thoai;
        $item->gioi_tinh = $gioi_tinh;
        $item->ngay_sinh = $ngay_sinh;

        if ($tinh_trang)
        {
            $item->tinh_trang = $tinh_trang;
        }

        $item->save();

        return $item;
    }

    public function dangKyLop(array $data)
    {
        $hoc_vien_id = $data['hoc_vien_id'];
        $lop_hoc_id = $data['lop_hoc_id'];
        $ngay_bat_dau = $data['ngay_bat_dau'] ?? now();
        $tinh_trang = 1;

        $item = new LopHocVien();
        $item->hoc_vien_id = $hoc_vien_id;
        $item->lop_hoc_id = $lop_hoc_id;
        $item->tinh_trang = $tinh_trang;
        $item->ngay_bat_dau = $ngay_bat_dau;
        $item->save();
        return $item;
    }

    public function delete()
    {

    }
}