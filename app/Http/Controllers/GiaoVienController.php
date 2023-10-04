<?php

namespace App\Http\Controllers;

use App\Models\Enums\GioiTinh;
use App\Models\GiaoVien;
use App\Services\GiaoVienService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class GiaoVienController extends Controller
{
    public function __construct(
        protected GiaoVienService $giaoVienService
    ) { }
    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $sort = $request->input('sort', '');

        $query = GiaoVien::query();

        $query->when( !empty($filter['ten']), function ($q) use ($filter) {
            return $q->where( function ( $subQuery ) use ($filter) {
                $subQuery->where('ten', 'like', '%' . $filter['ten'] . '%')
                        ->orWhere('ho', 'like', '%' . $filter['ten'] . '%');
            } );
        } );

        $query->when( !empty($filter['email']), function ($q) use ($filter) {
            return $q->where('email', 'like', '%' . $filter['email'] . '%');
        } );

        $query->when( !empty($filter['dien_thoai']), function ($q) use ($filter) {
            return $q->where('dien_thoai', 'like', '%' . $filter['dien_thoai'] . '%');
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

        $listQuery = $query->paginate(10)->withQueryString();

        $list = $listQuery->through(fn ($item) => [
            'id' => $item->id,
            'ho_va_ten' => $item->ho_va_ten,
            'dien_thoai' => $item->dien_thoai,
            'email' => $item->email,
            'ngay_sinh' => $item->ngay_sinh->format('d/m/Y'),
            'gioi_tinh' =>  GioiTinh::from($item->gioi_tinh)->title(),
        ]);

        return Inertia::render('GiaoVien/GiaoVienIndex', [
            'list' => $list,
            'filter' => $filter,
            'sort' => $sort
        ]);
    }

    public function create()
    {
        return Inertia::render('GiaoVien/GiaoVienCreate');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ho' => 'required',
            'ten' => 'required',
            'email' => 'required|email',
            'dien_thoai' => 'required',
            'ngay_sinh' => 'required',
        ]);

        $item = new GiaoVien();
        $item->ho = $request->input('ho');
        $item->ten = $request->input('ten');
        $item->email = $request->input('email');
        $item->dien_thoai = $request->input('dien_thoai');
        $item->ngay_sinh = $request->input('ngay_sinh');
        $item->gioi_tinh = $request->input('gioi_tinh');
        $item->save();

        return redirect()->route('giao-vien.index')
                ->with('message', 'Thêm mới thành công')
                ->with('status', 'success');
    }

    public function edit(GiaoVien $item)
    {
        $listLopHoc = $item->lop_hoc()->paginate(10);

        return Inertia::render('GiaoVien/GiaoVienEdit', [
            'giaoVien' => $item,
            'listLopHoc' => $listLopHoc,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ho' => 'required',
            'ten' => 'required',
            'email' => 'required|email',
            'dien_thoai' => 'required',
            'ngay_sinh' => 'required',
        ]);

        $item = GiaoVien::find($request->input('id'));
        $item->ho = $request->input('ho');
        $item->ten = $request->input('ten');
        $item->email = $request->input('email');
        $item->dien_thoai = $request->input('dien_thoai');
        $item->ngay_sinh = $request->input('ngay_sinh');
        $item->gioi_tinh = $request->input('gioi_tinh');
        $item->save();

        return back()->withInput()
                ->with('message', 'Thêm mới thành công')
                ->with('status', 'success');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');

        $item = GiaoVien::with('lop_hoc')->find($id);

        if ( $item->lop_hoc()->exists() ) {
            return back()->withInput()
                ->with('message', "Không thể xoá giáo viên đang có lớp")
                ->with('status', 'error');
        }

        GiaoVien::destroy($id);

        return Redirect::back()->with('status', 'success')->with('message', 'Giáo viên đã được xoá');
    }
}
