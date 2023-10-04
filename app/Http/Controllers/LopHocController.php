<?php

namespace App\Http\Controllers;

use App\Models\ChiNhanh;
use App\Models\GiaoVien;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Services\HocVienService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LopHocController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $sort = $request->input('sort', '');

        $query = LopHoc::with(['chi_nhanh', 'giao_vien']);

        $query->when( !empty($filter['chi_nhanh_id']), function ($q) use ($filter) {
            return $q->where('chi_nhanh_id', $filter['chi_nhanh_id']);
        } );

        $query->when( !empty($filter['giao_vien_id']), function ($q) use ($filter) {
            return $q->where('giao_vien_id', $filter['giao_vien_id']);
        } );

        switch ($sort) {
            case 'ten':
                $query->orderBy('ten');
                break;
            case '-ten':
                $query->orderBy('ten', 'desc');
                break;
            case 'id':
                $query->orderBy('id');
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $list = $query->paginate(10)->withQueryString();
        $list->through(function($item) {
            return [
                'id' => $item->id,
                'ten' => $item->ten,
                'ca_hoc' => $item->ca_hoc,
                'chi_nhanh' => $item->chi_nhanh,
                'giao_vien' => $item->giao_vien,
            ];
        });


        $listChiNhanh = ChiNhanh::orderBy('ten')->get();
        $listGiaoVien = GiaoVien::orderBy('ten')->get();

        return Inertia::render('LopHoc/LopHocIndex', [
            'list' => $list,
            'filter' => $filter,
            'sort' => $sort,
            'listChiNhanh' => $listChiNhanh,
            'listGiaoVien' => $listGiaoVien,
        ]);
    }

    public function create()
    {
        $listChiNhanh = ChiNhanh::orderBy('ten')->get();
        $listGiaoVien = GiaoVien::orderBy('ten')->get();

        return Inertia::render('LopHoc/LopHocCreate', [
            'listGiaoVien' => $listGiaoVien,
            'listChiNhanh' => $listChiNhanh,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten' => 'required',
            'ca_hoc' => 'required',
            'giao_vien_id' => 'required',
            'chi_nhanh_id' => 'required',
        ]);

        $ten = $request->input('ten');
        $ca_hoc = $request->input('ca_hoc');
        $giao_vien_id = $request->input('giao_vien_id');
        $chi_nhanh_id = $request->input('chi_nhanh_id');

        $item = new LopHoc();
        $item->ten = $ten;
        $item->ca_hoc = $ca_hoc;
        $item->giao_vien_id = $giao_vien_id;
        $item->chi_nhanh_id = $chi_nhanh_id;
        $item->save();

        return redirect()->route('lop-hoc.index')
                ->with('message', 'Thêm mới thành công')
                ->with('status', 'success');
    }

    public function edit(Request $request, LopHoc $item)
    {
        $listHocVien = $item->hoc_vien()->orderBy('ten')->paginate(10);
        $listChiNhanh = ChiNhanh::orderBy('ten')->get();
        $listGiaoVien = GiaoVien::orderBy('ten')->get();

        return Inertia::render('LopHoc/LopHocEdit', [
            'lopHoc' => $item,
            'listHocVien' => $listHocVien,
            'listGiaoVien' => $listGiaoVien,
            'listChiNhanh' => $listChiNhanh,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ten' => 'required',
            'ca_hoc' => 'required',
            'giao_vien_id' => 'required',
            'chi_nhanh_id' => 'required',
        ]);

        $id = $request->input('id');
        $ten = $request->input('ten');
        $ca_hoc = $request->input('ca_hoc');
        $giao_vien_id = $request->input('giao_vien_id');
        $chi_nhanh_id = $request->input('chi_nhanh_id');

        $item = LopHoc::find($id);
        $item->ten = $ten;
        $item->ca_hoc = $ca_hoc;
        $item->giao_vien_id = $giao_vien_id;
        $item->chi_nhanh_id = $chi_nhanh_id;
        $item->save();

        return back()->withInput()
                ->with('message', 'Cập nhật thành công')
                ->with('status', 'success');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $item = LopHoc::find($id);

        if ($item->hoc_phi()->exists()) {
            return back()->withInput()
                ->with('message', "Không thể xoá lớp đã có người đóng học phí")
                ->with('status', 'error');
        }

        $item->delete();
        return back()->withInput()
                ->with('message', 'Xoá lớp thành công')
                ->with('status', 'success');
    }

}
