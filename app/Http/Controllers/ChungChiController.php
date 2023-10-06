<?php

namespace App\Http\Controllers;

use App\Models\ChungChi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChungChiController extends Controller
{
    public function index(Request $request)
    {
        $list = ChungChi::paginate(10)->withQueryString();
        return Inertia::render('ChungChi/ChungChiIndex', [
            'list' => $list
        ]);
    }

    public function create()
    {
        return Inertia::render('ChungChi/ChungChiCreate');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten' => 'required'
        ]);

        $item = new ChungChi();
        $item->ten = $request->input('ten');
        $item->mo_ta = $request->input('mo_ta');
        $item->save();

        return redirect()->route('chung-chi.index')
                ->with('message', 'Thêm mới thành công')
                ->with('status', 'success');
    }

    public function edit(ChungChi $item)
    {
        $lichThi = $item->lich_thi()->orderBy('ngay_thi')->paginate(10);

        $lichThi->transform(function ($item) {
            return [
                'id' => $item->id,
                'ngay_thi' => $item->ngay_thi->format('d/m/Y H:i'),
                'dia_diem' => $item->dia_diem,
            ];
        });

        return Inertia::render('ChungChi/ChungChiEdit', [
            'chungChi' => $item,
            'lichThi' => $lichThi
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ten' => 'required'
        ]);

        $id = $request->input('id');
        $item = ChungChi::find($id);
        $item->ten = $request->input('ten');
        $item->mo_ta = $request->input('mo_ta');
        $item->save();

        return back()->withInput()
                ->with('message', 'Cập nhật thành công')
                ->with('status', 'success');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $item = ChungChi::find($id);

        if ( $item->lich_thi()->exists() ) {
            return back()->withInput()
                ->with('message', "Không thể xoá chứng chỉ đang có lịch thi")
                ->with('status', 'error');
        }

        $item->delete();
        return back()->withInput()
                ->with('message', 'Xoá chứng chỉ thành công')
                ->with('status', 'success');
    }

}
