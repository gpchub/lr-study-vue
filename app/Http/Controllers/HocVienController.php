<?php

namespace App\Http\Controllers;

use App\Models\ChungChi;
use App\Models\Enums\GioiTinh;
use App\Models\Enums\KetQuaThiChungChi;
use App\Models\Enums\TinhTrangHocVien;
use App\Models\Enums\TinhTrangThiChungChi;
use App\Models\HocPhi;
use App\Models\HocVien;
use App\Models\LichThi;
use App\Models\LichThiHocVien;
use App\Models\LopHoc;
use App\Models\LopHocVien;
use App\Services\HocVienService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HocVienController extends Controller
{
    public function __construct(
        protected HocVienService $hocVienService
    )
    {

    }

    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $sort = $request->input('sort', '');

        $query = HocVien::query();

        $query->when( !empty($filter['lop']), function ($q) use ($filter) {
            return $q->whereHas('lop_hoc', function ($subQuery) use ($filter) {
                $subQuery->where('lop_hoc.id', $filter['lop']);
            });
        } );

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

        $listLopHoc = LopHoc::orderBy('ten')->get();

        $list = $listQuery->through(fn ($item) => [
            'id' => $item->id,
            'ho_va_ten' => $item->ho_va_ten,
            'email' => $item->email,
            'dien_thoai' => $item->dien_thoai,
            'ngay_sinh' => $item->ngay_sinh->format('d/m/Y'),
            'gioi_tinh' => GioiTinh::from($item->gioi_tinh)->title(),
        ]);

        return Inertia::render('HocVien/HocVienIndex', [
            'list' => $list,
            'filter' => $filter,
            'sort' => $sort,
            'listLopHoc' => $listLopHoc
        ]);
    }

    public function create()
    {
        return Inertia::render('HocVien/HocVienCreate');
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

        $item = new HocVien();
        $item->ho = $request->input('ho');
        $item->ten = $request->input('ten');
        $item->email = $request->input('email');
        $item->dien_thoai = $request->input('dien_thoai');
        $item->ngay_sinh = $request->input('ngay_sinh');
        $item->gioi_tinh = $request->input('gioi_tinh');
        $item->save();

        return redirect()->route('hoc-vien.index')
                ->with('message', 'Thêm mới thành công')
                ->with('status', 'success');
    }

    public function edit(HocVien $item)
    {
        $listLopHoc = LopHoc::orderBy('ten')->get();

        $listLopHocVienIds = $item->lop_hoc->pluck('id');
        $listLopHocVien = $item->lop_hoc->transform(fn($item) => [
            'id' => $item->id,
            'ten' => $item->ten,
            //pivot
            'lop_hoc_vien_id' => $item->pivot->id,
            'hoc_vien_id' => $item->pivot->hoc_vien_id,
            'lop_hoc_id' => $item->pivot->lop_hoc_id,
            'ngay_bat_dau' => $item->pivot->ngay_bat_dau->format('d/m/Y'),
        ])->all();

        $listLichThiHocVienIds = $item->lich_thi->pluck('id');
        $listLichThiHocVien = $item->lich_thi()->with('chung_chi')->orderBy('ngay_thi')->get()->transform(fn ($item) => [
            'id' => $item->id,
            'ngay_thi' => $item->ngay_thi->format('d/m/Y - H:i'),
            'dia_diem' => $item->dia_diem,
            'ten_chung_chi' => $item->chung_chi->ten,
            'chung_chi_id' => $item->chung_chi_id,
            //pivot
            'lich_thi_hoc_vien_id' => $item->pivot->id,
            'hoc_vien_id' => $item->pivot->hoc_vien_id,
            'lich_thi_id' => $item->pivot->lich_thi_id,
            'tinh_trang' => $item->pivot->tinh_trang,
            'ket_qua' => $item->pivot->ket_qua,
            'tinh_trang_text' => TinhTrangThiChungChi::from($item->pivot->tinh_trang)->title(),
            'ket_qua_text' => KetQuaThiChungChi::from($item->pivot->ket_qua)->title(),
        ]);

        $listLichThi = LichThi::with('chung_chi')->orderBy('ngay_thi')->get();
        $listLichThi->transform(fn ($item) => [
            'id' => $item->id,
            'chung_chi_id' => $item->chung_chi_id,
            'ngay_thi' => $item->ngay_thi->format('d/m/Y - H:i'),
            'dia_diem' => $item->dia_diem,
            'chung_chi' => $item->chung_chi
        ]);

        return Inertia::render('HocVien/HocVienEdit', [
            'hocVien' => $item,
            'listLopHocVien' => $listLopHocVien,
            'listLopHocVienIds' => $listLopHocVienIds,
            'listLopHoc' => $listLopHoc,
            'listLichThiHocVien' => $listLichThiHocVien,
            'listLichThiHocVienIds' => $listLichThiHocVienIds,
            'listLichThi' => $listLichThi,
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

        $item = HocVien::find($request->input('id'));
        $item->ho = $request->input('ho');
        $item->ten = $request->input('ten');
        $item->email = $request->input('email');
        $item->dien_thoai = $request->input('dien_thoai');
        $item->ngay_sinh = $request->input('ngay_sinh');
        $item->gioi_tinh = $request->input('gioi_tinh');
        $item->save();

        return back()->withInput()
                ->with('message', 'Cập nhật thành công')
                ->with('status', 'success');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $item = HocVien::find($id);

        if ($item->hoc_phi->count() > 0) {
            return back()->withInput()
                ->with('message', "Không thể xoá học viên đã đóng học phí")
                ->with('status', 'error');
        }

        $item->delete();
        return back()->withInput()
                ->with('message', 'Xoá học viên thành công')
                ->with('status', 'success');
    }

    public function dangKyLop(Request $request)
    {
        $validated = $request->validate([
            'hoc_vien_id' => 'required|numeric',
            'lop_hoc_id' => 'required|numeric',
        ]);

        $this->hocVienService->dangKyLop($request->all());

        return back()->withInput()
                ->with('message', 'Đăng ký lớp thành công')
                ->with('status', 'success');
    }

    public function xoaLop(Request $request)
    {
        $id = $request->input('id');
        $item = LopHocVien::find($id);

        if ( HocPhi::where('hoc_vien_id', $item->hoc_vien_id)
                ->where('lop_hoc_id', $item->lop_hoc_id)
                ->exists()
        ) {
            return back()->withInput()
                ->with('message', "Không thể xoá lớp đã đóng học phí")
                ->with('status', 'error');
        }

        $item->delete();

        return back()->withInput()
                ->with('message', 'Xoá lớp thành công')
                ->with('status', 'success');
    }

    public function dongHocPhi(Request $request)
    {
        $validated = $request->validate([
            'hoc_vien_id' => 'required|numeric',
            'lop_hoc_id' => 'required|numeric',
            'so_tien' => 'required|numeric',
            'thang' => 'required|numeric',
            'nam' => 'required|numeric',
        ]);

        $hoc_vien_id = $request->input('hoc_vien_id');
        $lop_hoc_id = $request->input('lop_hoc_id');
        $so_tien = $request->input('so_tien');
        $thang = $request->input('thang');
        $nam = $request->input('nam');
        $ngay_dong = $request->input('ngay_dong');

        $checkHocPhiExists = HocPhi::where('hoc_vien_id', $hoc_vien_id)
                    ->where('lop_hoc_id', $lop_hoc_id)
                    ->where('thang', $thang)
                    ->where('nam', $nam)
                    ->count();

        if ($checkHocPhiExists) {
            return back()->withInput()
                ->with('message', "Lớp này học viên đã đóng học phí tháng {$thang}/{$nam}")
                ->with('status', 'error');
        }

        $item = HocPhi::create([
            'hoc_vien_id' => $hoc_vien_id,
            'lop_hoc_id' => $lop_hoc_id,
            'so_tien' => $so_tien,
            'thang' => $thang,
            'nam' => $nam,
            'ngay_dong' => $ngay_dong,
        ]);

        return back()->withInput()
                ->with('message', 'Đóng học phí thành công')
                ->with('status', 'success');
    }

    public function xemHocPhi(Request $request)
    {
        $hoc_vien_id = $request->input('hoc_vien_id');
        $lop_hoc_id = $request->input('lop_hoc_id');

        $list = HocPhi::where('hoc_vien_id', $hoc_vien_id)
                        ->where('lop_hoc_id', $lop_hoc_id)
                        ->orderBy('nam', 'desc')
                        ->orderBy('thang', 'desc')
                        ->get();

        $ds = $list->transform(fn ($item) => [
            'id' => $item->id,
            'lop_hoc' => $item->lop_hoc,
            'hoc_vien' => $item->hoc_vien,
            'so_tien' => number_format($item->so_tien, 0, ',', '.'),
            'thang' => $item->thang,
            'nam' => $item->nam,
            'ngay_dong' => $item->ngay_dong->format('d/m/Y'),
        ]);

        return response()->json($ds);
    }

    public function inDanhSach()
    {
        $list = HocVien::orderBy('ten')->get();
        return Inertia::render('HocVien/InDanhSach', [
            'list' => $list
        ]);
    }

    public function dangKyThi(Request $request)
    {
        $validated = $request->validate([
            'hoc_vien_id' => 'required|numeric',
            'lich_thi_id' => 'required|numeric',
        ]);

        $hoc_vien_id = $request->input('hoc_vien_id');
        $lich_thi_id = $request->input('lich_thi_id');

        if ( LichThiHocVien::where('hoc_vien_id', $hoc_vien_id)
                ->where('lich_thi_id', $lich_thi_id)
                ->exists()
        ) {
            return back()->withInput()
                ->with('message', "Học viên đã đăng ký đợt thi này")
                ->with('status', 'error');
        }

        LichThiHocVien::create([
            'hoc_vien_id' => $hoc_vien_id,
            'lich_thi_id' => $lich_thi_id,
        ]);

        return back()->withInput()
                ->with('message', 'Đăng ký khi thành công')
                ->with('status', 'success');
    }

    public function updateLichThi(Request $request)
    {
        $validated = $request->validate([
            'lich_thi_hoc_vien_id' => 'required'
        ]);

        $id = $request->input('lich_thi_hoc_vien_id');

        $item = LichThiHocVien::find($id);
        $item->tinh_trang = $request->input('tinh_trang');
        $item->ket_qua = $request->input('ket_qua');
        $item->save();

        return back()->withInput()
                ->with('message', 'Cập nhật chứng chỉ học viên thành công')
                ->with('status', 'success');
    }

    public function xoaLichThi(Request $request)
    {
        $id = $request->input('id');
        $item = LichThiHocVien::find($id);
        $item->delete();
        return back()->withInput()
                ->with('message', 'Xoá chứng chỉ học viên thành công')
                ->with('status', 'success');
    }

}
