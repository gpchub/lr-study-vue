<?php

namespace App\Http\Controllers;

use App\Models\ChungChi;
use App\Models\LichThi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LichThiController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $sort = $request->input('sort', '');

        $query = LichThi::with('chung_chi');

        $query->when( !empty($filter['chung_chi_id']), function ($q) use ($filter) {
            return $q->where('chung_chi_id', $filter['chung_chi_id']);
        } );

        $query->when( empty($filter['show_all']), function ($q) {
            return $q->where('ngay_thi', '>=', today());
        } );

        switch ($sort) {
            case 'ngay_thi':
                $query->orderBy('ngay_thi');
                break;
            case '-ngay_thi':
                $query->orderBy('ngay_thi', 'desc');
                break;
            case 'id':
                $query->orderBy('id');
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $list = $query->paginate(10)->withQueryString();
        $list->through(function ($item) {
            return [
                'id' => $item->id,
                'ngay_thi' => $item->ngay_thi->format('d/m/Y'),
                'gio_thi' => $item->ngay_thi->format('H:i'),
                'dia_diem' => $item->dia_diem,
                'chung_chi' => $item->chung_chi,
            ];
        });

        $listChungChi = ChungChi::orderBy('ten')->get();

        return Inertia::render('LichThi/LichThiIndex', [
            'list' => $list,
            'listChungChi' => $listChungChi,
            'filter' => $filter,
            'sort' => $sort
        ]);
    }

    public function create()
    {
        $listChungChi = ChungChi::orderBy('ten')->get();

        return Inertia::render('LichThi/LichThiCreate', [
            'listChungChi' => $listChungChi
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'chung_chi_id' => 'required|numeric',
            'ngay_thi' => 'required',
            'dia_diem' => 'required',
        ]);

        $item = new LichThi();
        $item->chung_chi_id = $request->input('chung_chi_id');
        $item->ngay_thi = $request->input('ngay_thi');
        $item->dia_diem = $request->input('dia_diem');
        $item->save();

        return redirect()->route('lich-thi.index')
                ->with('message', 'Thêm mới thành công')
                ->with('status', 'success');
    }

    public function edit(LichThi $item)
    {
        $listHocVien = $item->hoc_vien()->paginate(10);
        $listChungChi = ChungChi::orderBy('ten')->get();

        return Inertia::render('LichThi/LichThiEdit', [
            'lichThi' => $item,
            'listHocVien' => $listHocVien,
            'listChungChi' => $listChungChi,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'chung_chi_id' => 'required|numeric',
            'ngay_thi' => 'required',
            'dia_diem' => 'required',
        ]);

        $id = $request->input('id');
        $item = LichThi::find($id);
        $item->chung_chi_id = $request->input('chung_chi_id');
        $item->ngay_thi = $request->input('ngay_thi');
        $item->dia_diem = $request->input('dia_diem');
        $item->save();

        return back()->withInput()
                ->with('message', 'Chỉnh sửa thành công')
                ->with('status', 'success');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $item = LichThi::find($id);

        if ($item->hoc_vien()->exists()) {
            return back()->withInput()
                ->with('message', "Không thể xoá lịch thi đã có học viên đăng ký")
                ->with('status', 'error');
        }

        $item->delete();
        return back()->withInput()
                ->with('message', 'Xoá lịch thi thành công')
                ->with('status', 'success');
    }
}
