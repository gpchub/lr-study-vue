<?php

namespace App\Http\Controllers;

use App\Models\Enums\GioiTinh;
use App\Models\Enums\TinhTrangHocVien;
use App\Models\HocPhi;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Models\LopHocVien;
use App\Services\HocVienService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HocPhiController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $sort = $request->input('sort', '');

        $query = HocPhi::with('lop_hoc', 'hoc_vien');

        $query->when( !empty($filter['lop_hoc_id']), function ($q) use ($filter) {
            return $q->where('lop_hoc_id', $filter['lop_hoc_id']);
        } );

        $query->when( !empty($filter['hoc_vien_id']), function ($q) use ($filter) {
            return $q->where('hoc_vien_id', $filter['hoc_vien_id']);
        } );

        if ( $sort == 'ten_hoc_vien' || $sort == '-ten_hoc_vien' ) {
            $query->join('hoc_vien', 'hoc_phi.hoc_vien_id', '=', 'hoc_vien.id');
        }

        if ( $sort == 'ten_lop' || $sort == '-ten_lop' ) {
            $query->join('lop_hoc', 'hoc_phi.lop_hoc_id', '=', 'lop_hoc.id');
        }

        switch ($sort) {
            case 'ten_hoc_vien':
                $query->orderBy('hoc_vien.ten');
                break;
            case '-ten_hoc_vien':
                $query->orderBy('hoc_vien.ten', 'desc');
                break;
            case 'ten_lop':
                $query->orderBy('lop_hoc.ten');
                break;
            case '-ten_lop':
                $query->orderBy('lop_hoc.ten', 'desc');
                break;
            case 'ngay_dong':
                $query->orderBy('ngay_dong');
                break;
            case '-ngay_dong':
                $query->orderBy('ngay_dong', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $list = $query->paginate(10)->withQueryString();

        $ds = $list->through(fn ($item) => [
            'id' => $item->id,
            'lop_hoc' => $item->lop_hoc,
            'hoc_vien' => $item->hoc_vien,
            'so_tien' => number_format($item->so_tien, 0, ',', '.'),
            'thang' => $item->thang,
            'nam' => $item->nam,
            'ngay_dong' => $item->ngay_dong->format('d/m/Y'),
        ]);

        $listHocVien = HocVien::orderBy('ten')->get();
        $listLopHoc = LopHoc::orderBy('ten')->get();

        return Inertia::render('HocPhi/HocPhiIndex', [
            'list' => $ds,
            'listHocVien' => $listHocVien,
            'listLopHoc' => $listLopHoc,
            'filter' => $filter,
            'sort' => $sort
        ]);
    }

    public function edit(HocPhi $item)
    {
        return Inertia::render('HocPhi/HocPhiEdit', [
            'hocPhi' => $item->load('lop_hoc', 'hoc_vien'),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'so_tien' => 'required|numeric',
            'hoc_vien_id' => 'required|numeric',
            'lop_hoc_id' => 'required|numeric',
        ]);

        $id = $request->input('id');

        $item = HocPhi::find($id);
        $item->so_tien = $request->input('so_tien', 0);
        $item->ngay_dong = $request->input('ngay_dong', now());
        $item->save();

        return back()->withInput()
                ->with('message', 'Cập nhật thành công')
                ->with('status', 'success');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        HocPhi::destroy($id);
        return back()->withInput()
                ->with('message', 'Xoá thành công')
                ->with('status', 'success');
    }
}
