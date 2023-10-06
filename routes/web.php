<?php

use App\Http\Controllers\ChiNhanhController;
use App\Http\Controllers\ChungChiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GiaoVienController;
use App\Http\Controllers\HocPhiController;
use App\Http\Controllers\HocVienController;
use App\Http\Controllers\LichThiController;
use App\Http\Controllers\LopHocController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThongKeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::prefix('chi-nhanh')->name('chi-nhanh.')->group(function() {
    Route::get('/', [ChiNhanhController::class, 'index'])->name('index');
    Route::get('/create', [ChiNhanhController::class, 'create'])->name('create');
    Route::get('/edit/{item}', [ChiNhanhController::class, 'edit'])->name('edit');
    Route::post('/store', [ChiNhanhController::class, 'store'])->name('store');
    Route::patch('/update', [ChiNhanhController::class, 'update'])->name('update');
    Route::post('/delete', [ChiNhanhController::class, 'delete'])->name('delete');
});

Route::prefix('hoc-vien')->name('hoc-vien.')->group(function() {
    Route::get('/', [HocVienController::class, 'index'])->name('index');
    Route::get('/create', [HocVienController::class, 'create'])->name('create');
    Route::get('/edit/{item}', [HocVienController::class, 'edit'])->name('edit');
    Route::post('/store', [HocVienController::class, 'store'])->name('store');
    Route::patch('/update', [HocVienController::class, 'update'])->name('update');
    Route::post('/delete', [HocVienController::class, 'delete'])->name('delete');

    Route::post('/dang-ky-lop', [HocVienController::class, 'dangKyLop'])->name('dang-ky-lop');
    Route::post('/xoa-lop', [HocVienController::class, 'xoaLop'])->name('xoa-lop');
    Route::post('/dong-hoc-phi', [HocVienController::class, 'dongHocPhi'])->name('dong-hoc-phi');
    Route::get('/xem-hoc-phi', [HocVienController::class, 'xemHocPhi'])->name('xem-hoc-phi');
    Route::get('/in-danh-sach', [HocVienController::class, 'inDanhSach'])->name('in-danh-sach');

    Route::post('/dang-ky-thi', [HocVienController::class, 'dangKyThi'])->name('dang-ky-thi');
    Route::post('/update-lich-thi', [HocVienController::class, 'updateLichThi'])->name('update-lich-thi');
    Route::post('/xoa-lich-thi', [HocVienController::class, 'xoaLichThi'])->name('xoa-lich-thi');
});

Route::prefix('giao-vien')->name('giao-vien.')->group(function() {
    Route::get('/', [GiaoVienController::class, 'index'])->name('index');
    Route::get('/create', [GiaoVienController::class, 'create'])->name('create');
    Route::get('/edit/{item}', [GiaoVienController::class, 'edit'])->name('edit');
    Route::post('/store', [GiaoVienController::class, 'store'])->name('store');
    Route::patch('/update', [GiaoVienController::class, 'update'])->name('update');
    Route::post('/delete', [GiaoVienController::class, 'delete'])->name('delete');
});

Route::prefix('lop-hoc')->name('lop-hoc.')->group(function() {
    Route::get('/', [LopHocController::class, 'index'])->name('index');
    Route::get('/create', [LopHocController::class, 'create'])->name('create');
    Route::get('/edit/{item}', [LopHocController::class, 'edit'])->name('edit');
    Route::post('/store', [LopHocController::class, 'store'])->name('store');
    Route::patch('/update', [LopHocController::class, 'update'])->name('update');
    Route::post('/delete', [LopHocController::class, 'delete'])->name('delete');
});

Route::prefix('hoc-phi')->name('hoc-phi.')->group(function() {
    Route::get('/', [HocPhiController::class, 'index'])->name('index');
    Route::get('/create', [HocPhiController::class, 'create'])->name('create');
    Route::get('/edit/{item}', [HocPhiController::class, 'edit'])->name('edit');
    Route::post('/store', [HocPhiController::class, 'store'])->name('store');
    Route::patch('/update', [HocPhiController::class, 'update'])->name('update');
    Route::post('/delete', [HocPhiController::class, 'delete'])->name('delete');
});

Route::prefix('thong-ke')->name('thong-ke.')->group(function() {
    Route::get('/', [ThongKeController::class, 'index'])->name('index');
    Route::get('/get-tong-hoc-phi', [ThongKeController::class, 'getTongHocPhi'])->name('get-tong-hoc-phi');
    Route::get('/get-so-hoc-vien-theo-lop', [ThongKeController::class, 'getSoHocVienTheoLop'])->name('get-so-hoc-vien-theo-lop');
    Route::get('/get-tong-hoc-phi-theo-lop', [ThongKeController::class, 'getTongHocPhiTheoLop'])->name('get-tong-hoc-phi-theo-lop');
    Route::get('/get-top-hoc-vien-theo-hoc-phi', [ThongKeController::class, 'getTopHocVienTheoHocPhi'])->name('get-top-hoc-vien-theo-hoc-phi');
});

Route::prefix('chung-chi')->name('chung-chi.')->group(function() {
    Route::get('/', [ChungChiController::class, 'index'])->name('index');
    Route::get('/create', [ChungChiController::class, 'create'])->name('create');
    Route::get('/edit/{item}', [ChungChiController::class, 'edit'])->name('edit');
    Route::post('/store', [ChungChiController::class, 'store'])->name('store');
    Route::patch('/update', [ChungChiController::class, 'update'])->name('update');
    Route::post('/delete', [ChungChiController::class, 'delete'])->name('delete');
});

Route::prefix('lich-thi')->name('lich-thi.')->group(function() {
    Route::get('/', [LichThiController::class, 'index'])->name('index');
    Route::get('/create', [LichThiController::class, 'create'])->name('create');
    Route::get('/edit/{item}', [LichThiController::class, 'edit'])->name('edit');
    Route::post('/store', [LichThiController::class, 'store'])->name('store');
    Route::patch('/update', [LichThiController::class, 'update'])->name('update');
    Route::post('/delete', [LichThiController::class, 'delete'])->name('delete');
});