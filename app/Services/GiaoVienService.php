<?php

namespace App\Services;

use App\Models\GiaoVien;
use App\Models\LopHoc;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class GiaoVienService
{
    public function getAll($data = [])
    {
        $sort = isset($data['sort']) ? $data['sort'] : '';
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        $pageSize = $data['page-size'] ?? 10;

        $query = GiaoVien::query();

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

        $list = $query->paginate($pageSize)->withQueryString();

        return $list;
    }

    public function getById($id)
    {
        return GiangVien::find($id);
    }

    public function create($data = [])
    {
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';

        $item = GiangVien::create([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
        ]);

        return $item;
    }

    public function update($data = [])
    {
        $id = $data['id'] ?? '';
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';

        $item = $this->getById($id);
        $item->name = $name;
        $item->email = $email;
        $item->phone = $phone;
        $item->save();

        return $item;
    }

    public function delete($id)
    {
        GiangVien::destroy($id);
    }
}