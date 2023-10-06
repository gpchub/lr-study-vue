<?php

namespace Tests\Feature;

use App\Models\ChiNhanh;
use App\Models\ChungChi;
use App\Models\GiaoVien;
use App\Models\HocPhi;
use App\Models\HocVien;
use App\Models\LichThi;
use App\Models\LichThiHocVien;
use App\Models\LopHoc;
use App\Models\LopHocVien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HocVienTest extends TestCase
{
    use RefreshDatabase;

    public function test_hoc_vien_index_is_displayed(): void
    {
        $response = $this->get( route('hoc-vien.index') );

        $response->assertStatus(200);
    }

    public function test_hoc_vien_create_is_displayed(): void
    {
        $response = $this->get(route('hoc-vien.create'));

        $response->assertStatus(200);
    }

    public function test_hoc_vien_can_be_validated_on_creating(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('hoc-vien.create'))
            ->post(route('hoc-vien.store'), []);

        $response
            ->assertSessionHasErrors([
                'ho',
                'ten',
                'email',
                'dien_thoai',
                'ngay_sinh',
            ])
            ->assertRedirect( route('hoc-vien.create') );
    }

    public function test_hoc_vien_can_be_created(): void
    {
        $user = User::factory()->create();
        $postData = $this->postData();

        $response = $this
            ->actingAs($user)
            ->post(route('hoc-vien.store'), $postData);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('hoc-vien.index'));

        $item = HocVien::latest()->first();

        $this->assertSame($postData['ho'], $item->ho);
        $this->assertSame($postData['ten'], $item->ten);
        $this->assertSame($postData['email'], $item->email);
        $this->assertSame($postData['dien_thoai'], $item->dien_thoai);
        $this->assertSame($postData['gioi_tinh'], $item->gioi_tinh);
        $this->assertSame($postData['ngay_sinh'], $item->ngay_sinh->format('Y/m/d'));
    }

    public function test_hoc_vien_edit_is_displayed(): void
    {
        $item = HocVien::factory()->create();
        $response = $this->get( route('hoc-vien.edit', $item->id) ) ;

        $response->assertStatus(200);
    }

    public function test_hoc_vien_can_be_validated_on_updating(): void
    {
        $user = User::factory()->create();
        $item = HocVien::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $item->id) )
            ->patch( route('hoc-vien.update'), []);

        $response
            ->assertSessionHasErrors([
                'ho',
                'ten',
                'email',
                'dien_thoai',
                'ngay_sinh',
            ])
            ->assertRedirect( route('hoc-vien.edit', $item->id) );
    }

    public function test_hoc_vien_can_be_updated(): void
    {
        $user = User::factory()->create();
        $item = HocVien::factory()->create();
        $postData = $this->postData($item->id);

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $item->id) )
            ->patch( route('hoc-vien.update'), $postData);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('hoc-vien.edit', $item->id) );

        $item->refresh();

        $this->assertSame($postData['ho'], $item->ho);
        $this->assertSame($postData['ten'], $item->ten);
        $this->assertSame($postData['email'], $item->email);
        $this->assertSame($postData['dien_thoai'], $item->dien_thoai);
        $this->assertSame($postData['gioi_tinh'], $item->gioi_tinh);
        $this->assertSame($postData['ngay_sinh'], $item->ngay_sinh->format('Y/m/d'));
    }

    public function test_hoc_vien_has_hoc_phi_can_not_be_deleted():void
    {
        $user = User::factory()->create();
        $hocVien = HocVien::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $lopHoc = LopHoc::factory()->create();
        $hocPhi = HocPhi::factory()->create([
            'hoc_vien_id' => $hocVien->id
        ]);

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.index') )
            ->post( route('hoc-vien.delete'), [
                'id' => $hocVien->id,
            ]);

        $response
            ->assertSessionHas('status', 'error')
            ->assertRedirect( route('hoc-vien.index') );

        $this->assertNotSoftDeleted($hocVien);
    }

    public function test_hoc_vien_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $item = HocVien::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.index') )
            ->post( route('hoc-vien.delete'), [
                'id' => $item->id
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('hoc-vien.index') );

        $this->assertSoftDeleted($item);
    }

    public function test_hoc_vien_can_be_validated_on_dang_ky_lop(): void
    {
        $user = User::factory()->create();
        $item = HocVien::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $item->id) )
            ->post( route('hoc-vien.dang-ky-lop'), []);

        $response
            ->assertSessionHasErrors([
                'hoc_vien_id',
                'lop_hoc_id'
            ])
            ->assertRedirect( route('hoc-vien.edit', $item->id) );
    }

    public function test_hoc_vien_can_dang_ky_lop(): void
    {
        $user = User::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $lopHoc = LopHoc::factory()->create();
        $item = HocVien::factory()->create();

        $postData = [
            'hoc_vien_id' => $item->id,
            'lop_hoc_id' => $lopHoc->id,
            'ngay_bat_dau' => '2023/10/10',
        ];

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $item->id) )
            ->post( route('hoc-vien.dang-ky-lop'), $postData);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('hoc-vien.edit', $item->id) );

        $lopHocVien = LopHocVien::latest()->first();

        $this->assertSame($postData['hoc_vien_id'], $lopHocVien->hoc_vien_id);
        $this->assertSame($postData['lop_hoc_id'], $lopHocVien->lop_hoc_id);
        $this->assertSame($postData['ngay_bat_dau'], $lopHocVien->ngay_bat_dau->format('Y/m/d'));
    }

    public function test_hoc_vien_has_hoc_phi_can_not_xoa_lop(): void
    {
        $user = User::factory()->create();
        $hocVien = HocVien::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $lopHoc = LopHoc::factory()->create();
        $lopHocVien = LopHocVien::factory()->create([
            'hoc_vien_id' => $hocVien->id,
            'lop_hoc_id' => $lopHoc->id,
        ]);
        $hocPhi = HocPhi::factory()->create([
            'hoc_vien_id' => $hocVien->id,
            'lop_hoc_id' => $lopHoc->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.xoa-lop'), [
                'id' => $lopHocVien->id
            ]);

        $response
            ->assertSessionHas('status', 'error')
            ->assertRedirect( route('hoc-vien.edit', $hocVien->id) );

        $this->assertModelExists($lopHocVien);
    }

    public function test_hoc_vien_can_xoa_lop(): void
    {
        $user = User::factory()->create();
        $hocVien = HocVien::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $lopHoc = LopHoc::factory()->create();
        $lopHocVien = LopHocVien::factory()->create([
            'hoc_vien_id' => $hocVien->id,
            'lop_hoc_id' => $lopHoc->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.xoa-lop'), [
                'id' => $lopHocVien->id
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', 'success')
            ->assertRedirect( route('hoc-vien.edit', $hocVien->id) );

        $this->assertModelMissing($lopHocVien);
    }

    public function test_hoc_vien_can_dong_hoc_phi(): void
    {
        $user = User::factory()->create();
        $hocVien = HocVien::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $lopHoc = LopHoc::factory()->create();

        /*----------  Test validation  ----------*/
        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.dong-hoc-phi'), []);

        $response
            ->assertSessionHasErrors([
                'hoc_vien_id',
                'lop_hoc_id',
                'so_tien',
                'nam',
                'thang'
            ])
            ->assertRedirect( route('hoc-vien.edit', $hocVien->id) );

        /*----------  Test create  ----------*/
        $postData = [
            'hoc_vien_id' => $hocVien->id,
            'lop_hoc_id' => $lopHoc->id,
            'so_tien' => 100,
            'nam' => 2023,
            'thang' => 10,
            'ngay_dong' => '2023/10/10',
        ];

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.dong-hoc-phi'), $postData);

        $hocPhi = HocPhi::latest()->first();

        $this->assertSame($postData['hoc_vien_id'], $hocPhi->hoc_vien_id);
        $this->assertSame($postData['lop_hoc_id'], $hocPhi->lop_hoc_id);
        $this->assertSame($postData['so_tien'], $hocPhi->so_tien);
        $this->assertSame($postData['thang'], $hocPhi->thang);
        $this->assertSame($postData['nam'], $hocPhi->nam);
        $this->assertSame($postData['ngay_dong'], $hocPhi->ngay_dong->format('Y/m/d'));

        /*----------  Test dong hoc phi lop da dong hoc phi  ----------*/
        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.dong-hoc-phi'), $postData);

        $response
            ->assertSessionHas('status', 'error')
            ->assertRedirect( route('hoc-vien.edit', $hocVien->id) );
    }

    public function test_hoc_vien_can_xem_hoc_phi(): void
    {
        $response = $this->get( route('hoc-vien.xem-hoc-phi') );

        $response->assertStatus(200);
    }

    public function test_hoc_vien_can_dang_ky_thi(): void
    {
        $user = User::factory()->create();
        $hocVien = HocVien::factory()->create();
        $chungChi = ChungChi::factory()->create();
        $lichThi = LichThi::factory()->create();

        /*----------  Test validation  ----------*/
        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.dang-ky-thi'), []);

        $response
            ->assertSessionHasErrors([
                'hoc_vien_id',
                'lich_thi_id',
            ])
            ->assertRedirect( route('hoc-vien.edit', $hocVien->id) );

        /*----------  Test create  ----------*/
        $postData = [
            'hoc_vien_id' => $hocVien->id,
            'lich_thi_id' => $lichThi->id,
        ];

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.dang-ky-thi'), $postData);

        $lichThiHocVien = LichThiHocVien::latest()->first();

        $this->assertSame($postData['hoc_vien_id'], $lichThiHocVien->hoc_vien_id);
        $this->assertSame($postData['lich_thi_id'], $lichThiHocVien->lich_thi_id);
        $this->assertSame(null, $lichThiHocVien->tinh_trang);
        $this->assertSame(null, $lichThiHocVien->ket_qua);

        /*----------  Test đăng ký thi ở lịch thi đã đăng ký  ----------*/
        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.dang-ky-thi'), $postData);

        $response
            ->assertSessionHas('status', 'error')
            ->assertRedirect( route('hoc-vien.edit', $hocVien->id) );
    }

    public function test_hoc_vien_can_update_lich_thi(): void
    {
        $user = User::factory()->create();
        $hocVien = HocVien::factory()->create();
        $chungChi = ChungChi::factory()->create();
        $lichThi = LichThi::factory()->create();
        $lichThiHocVien = LichThiHocVien::factory()->create([
            'hoc_vien_id' => $hocVien->id,
            'lich_thi_id' => $lichThi->id,
            'tinh_trang' => 0,
            'ket_qua' => 0
        ]);

        /*----------  Test validation  ----------*/
        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.update-lich-thi'), []);

        $response
            ->assertSessionHasErrors([
                'lich_thi_hoc_vien_id',
            ])
            ->assertRedirect( route('hoc-vien.edit', $hocVien->id) );

        /*----------  Test update  ----------*/
        $postData = [
            'tinh_trang' => 1,
            'ket_qua' => 1,
            'lich_thi_hoc_vien_id' => $lichThiHocVien->id,
        ];

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.update-lich-thi'), $postData);

        $lichThiHocVien->refresh();

        $this->assertSame($postData['tinh_trang'], $lichThiHocVien->tinh_trang);
        $this->assertSame($postData['ket_qua'], $lichThiHocVien->ket_qua);
    }

    public function test_hoc_vien_can_xoa_lich_thi(): void
    {
        $user = User::factory()->create();
        $hocVien = HocVien::factory()->create();
        $chungChi = ChungChi::factory()->create();
        $lichThi = LichThi::factory()->create();
        $lichThiHocVien = LichThiHocVien::factory()->create([
            'hoc_vien_id' => $hocVien->id,
            'lich_thi_id' => $lichThi->id,
            'tinh_trang' => 0,
            'ket_qua' => 0
        ]);

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.xoa-lich-thi'), [
                'id' => $lichThiHocVien->id
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('hoc-vien.edit', $hocVien->id));

        $this->assertModelMissing($lichThiHocVien);
    }

    protected function postData($id = null)
    {
        $data = [
            'ho' => 'Họ',
            'ten' => 'Tên',
            'email' => 'Email@email.dev',
            'dien_thoai' => '0909xxxxxx',
            'ngay_sinh' => '2000/01/01',
            'gioi_tinh' => 1,
        ];

        if ($id) {
            $data['id'] = $id;
        }

        return $data;
    }
}
