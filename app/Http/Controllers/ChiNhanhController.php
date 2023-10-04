<?php

namespace App\Http\Controllers;

use App\Models\ChiNhanh;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChiNhanhController extends Controller
{
    public function index()
    {
        $list = ChiNhanh::orderBy('ten', 'asc')->paginate(10);

        return Inertia::render('ChiNhanh/ChiNhanhIndex', [
            'list' => $list,
        ]);
    }

    public function create()
    {
        return Inertia::render('ChiNhanh/ChiNhanhCreate');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten' => 'required',
            'dia_chi' => 'required',
            'dien_thoai' => 'required',
            'email' => 'required|email',
        ]);

        $ten = $request->input('ten');
        $email = $request->input('email');
        $dien_thoai = $request->input('dien_thoai');
        $dia_chi = $request->input('dia_chi');

        $item = ChiNhanh::create([
            'ten' => $ten,
            'email' => $email,
            'dia_chi' => $dia_chi,
            'dien_thoai' => $dien_thoai,
        ]);

        return redirect()->route('chi-nhanh.index')
                ->with('message', 'Thêm mới thành công')
                ->with('status', 'success');
    }

    public function edit(ChiNhanh $item)
    {
        $danh_sach_lop = $item->lop_hoc()->get();

        return Inertia::render('ChiNhanh/ChiNhanhEdit', [
            'chiNhanh' => $item,
            'danhSachLop' => $danh_sach_lop
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ten' => 'required',
            'dia_chi' => 'required',
            'dien_thoai' => 'required',
            'email' => 'required|email',
        ]);

        $id = $request->input('id');
        $ten = $request->input('ten');
        $email = $request->input('email');
        $dien_thoai = $request->input('dien_thoai');
        $dia_chi = $request->input('dia_chi');

        $item = ChiNhanh::find($id);
        $item->ten = $ten;
        $item->email = $email;
        $item->dien_thoai = $dien_thoai;
        $item->dia_chi = $dia_chi;
        $item->save();

        return back()->withInput()
                ->with('message', 'Cập nhật thành công')
                ->with('status', 'success');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');

        if (ChiNhanh::find($id)->lop_hoc()->exists()) {
            return back()->withInput()
                ->with('message', 'Không thể xoá chi nhánh đang có lớp học.')
                ->with('status', 'error');
        }

        ChiNhanh::destroy($id);

        return back()->with('status', 'success')->with('message', 'Chi nhánh đã được xoá');
    }
}
