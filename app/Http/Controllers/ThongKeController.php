<?php

namespace App\Http\Controllers;

use App\Models\HocPhi;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Models\LopHocVien;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ThongKeController extends Controller
{
    public function index()
    {


        return Inertia::render('ThongKe/ThongKeIndex', [

        ]);
    }

    public function getTongHocPhi(Request $request)
    {
        $year = $request->input('year');
        $tongHocPhi = HocPhi::selectRaw('thang, sum(so_tien) as tong_tien')
                        ->where('nam', $year)
                        ->groupBy('thang')
                        ->get();
        return response()->json($tongHocPhi);
    }

    public function getSoHocVienTheoLop(Request $request)
    {
        $soHocVienTheoLop = LopHoc::select(['id', 'ten'])->withCount('hoc_vien')->get();
        return response()->json($soHocVienTheoLop);
    }

    public function getTopHocVienTheoHocPhi(Request $request)
    {
        $topHocVienTheoHocPhi = HocVien::select(['id', 'ho', 'ten'])
                                    ->withSum('hoc_phi', 'so_tien')
                                    ->orderBy('hoc_phi_sum_so_tien', 'desc')
                                    ->limit(10)
                                    ->get();
        return response()->json($topHocVienTheoHocPhi);
    }

    public function getTongHocPhiTheoLop(Request $request)
    {
        $year = $request->input('year');
        $tongHocPhiTheoLop = LopHoc::select(['id', 'ten',])
                                    ->withSum(['hoc_phi' => function($query) use($year) {
                                        $query->where('nam', $year);
                                    }], 'so_tien')
                                    ->get();
        return response()->json($tongHocPhiTheoLop);
    }
}
