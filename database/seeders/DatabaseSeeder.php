<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\ChiNhanh;
use App\Models\ChungChi;
use App\Models\GiaoVien;
use App\Models\HocPhi;
use App\Models\HocVien;
use App\Models\LichThi;
use App\Models\LopHoc;
use App\Models\LopHocVien;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(TruncateAllTable::class);

        \App\Models\User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@nls.dev',
        ]);

        \App\Models\User::factory()->create([
            'name' => 'user1',
            'email' => 'user1@nls.dev',
        ]);

        \App\Models\User::factory()->create([
            'name' => 'user2',
            'email' => 'user2@nls.dev',
        ]);

        ChiNhanh::factory()->create([
            'ten' => 'Thành Thái',
            'dia_chi' => '51/5 Đ. Thành Thái, Phường 14, Quận 10, TPHCM'
        ]);

        ChiNhanh::factory()->create([
            'ten' => 'Lý Thường Kiệt',
            'dia_chi' => '51/5 Đ. Lý Thường Kiệt, Phường 14, Quận 10, TPHCM'
        ]);

        GiaoVien::factory(10)->create();
        HocVien::factory(50)->create();

        $hocVienIds = HocVien::pluck('id');
        LopHoc::factory(10)
            ->create()
            ->each(function ($lopHoc) use ($hocVienIds) {
                $randomLopIds = fake()->randomElements($hocVienIds, fake()->numberBetween(10, 15));
                $randomLop = array_reduce($randomLopIds, function ($acc, $element) {
                    $acc[$element] = [
                        'ngay_bat_dau' => fake()->dateTimeBetween('-1 years')
                    ];
                    return $acc;
                }, []);
                $lopHoc->hoc_vien()->attach($randomLop);
            });

        $lopHocVien = LopHocVien::all();
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $lopHocVien->each(function($lop) use ($currentMonth, $currentYear) {
            $ngay_thang_bat_dau = $lop->ngay_bat_dau;
            $thang_bat_dau = $ngay_thang_bat_dau->month;
            $nam_bat_dau = $ngay_thang_bat_dau->year;
            $ngay_bat_dau = $ngay_thang_bat_dau->day;

            if ($nam_bat_dau == $currentYear) {
                for ($i = $thang_bat_dau; $i <= $currentMonth - 1; $i++) {
                    HocPhi::create([
                        'hoc_vien_id' => $lop->hoc_vien_id,
                        'lop_hoc_id' => $lop->lop_hoc_id,
                        'so_tien' => 1200000,
                        'thang' => $i,
                        'nam' => $currentYear,
                        'ngay_dong' => Carbon::createFromDate($currentYear, $i, $ngay_bat_dau)
                    ]);
                }
            } else { // $nam_bat_dau < $currentYear
                for ($y = $nam_bat_dau; $y <= $currentYear; $y++) {
                    $start = 1;
                    $end = 12;

                    if ($y == $nam_bat_dau && $y < $currentYear) {
                        $start = $thang_bat_dau;
                        $end = 12;
                    } else if ($y == $currentYear) {
                        $start = 1;
                        $end = $currentMonth - 1;
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        HocPhi::create([
                            'hoc_vien_id' => $lop->hoc_vien_id,
                            'lop_hoc_id' => $lop->lop_hoc_id,
                            'so_tien' => 1200000,
                            'thang' => $i,
                            'nam' => $y,
                            'ngay_dong' => Carbon::createFromDate($y, $i, $ngay_bat_dau)
                        ]);
                    }
                }
            }
        });

        // Schema::disableForeignKeyConstraints();
        // DB::table('lich_thi_hoc_vien')->truncate();
        // DB::table('lich_thi')->truncate();
        // DB::table('chung_chi')->truncate();
        // Schema::enableForeignKeyConstraints();

        ChungChi::factory(5)->create();
        $listHocVienIds = HocVien::pluck('id');
        LichThi::factory(50)->create()->each(function ($lich) use ($listHocVienIds) {
            $randomHocVien = fake()->randomElements(
                                    $listHocVienIds,
                                    fake()->numberBetween(5,10)
                                );
            $lich->hoc_vien()->attach($randomHocVien);

        });
    }
}
