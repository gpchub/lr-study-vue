<?php

namespace Tests\Feature;

use App\Models\ChiNhanh;
use App\Models\GiaoVien;
use App\Models\HocPhi;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LopHocTest extends TestCase
{
    use RefreshDatabase;

    public function test_lop_hoc_index_is_displayed(): void
    {
        $response = $this->get( route('lop-hoc.index') );

        $response->assertStatus(200);
    }

    public function test_lop_hoc_create_is_displayed(): void
    {
        $response = $this->get(route('lop-hoc.create'));

        $response->assertStatus(200);
    }

    public function test_lop_hoc_can_be_validated_on_creating(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('lop-hoc.create'))
            ->post(route('lop-hoc.store'), [
                'ten' => '',
            ]);

        $response
            ->assertSessionHasErrors([
                'ten' => 'The ten field is required.',
                'ca_hoc' => 'The ca hoc field is required.',
                'giao_vien_id' => 'The giao vien id field is required.',
                'chi_nhanh_id' => 'The chi nhanh id field is required.',
            ])
            ->assertRedirect( route('lop-hoc.create') );
    }

    public function test_lop_hoc_can_be_created(): void
    {
        $user = User::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('lop-hoc.store'), [
                'ten' => 'Tên',
                'ca_hoc' => 'ca hoc',
                'giao_vien_id' => $giaoVien->id,
                'chi_nhanh_id' => $chiNhanh->id,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('lop-hoc.index'));

        $item = LopHoc::latest()->first();

        $this->assertSame('Tên', $item->ten);
        $this->assertSame('ca hoc', $item->ca_hoc);
        $this->assertSame($giaoVien->id, $item->giao_vien_id);
        $this->assertSame($chiNhanh->id, $item->chi_nhanh_id);
    }

    public function test_lop_hoc_edit_is_displayed(): void
    {
        $giaoVien = GiaoVien::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $item = LopHoc::factory()->create();
        $response = $this->get( route('lop-hoc.edit', $item->id) ) ;

        $response->assertStatus(200);
    }

    public function test_lop_hoc_can_be_validated_on_updating(): void
    {
        $user = User::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $item = LopHoc::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('lop-hoc.edit', $item->id) )
            ->patch( route('lop-hoc.update'), [
                'ten' => '',
            ]);

        $response
            ->assertSessionHasErrors([
                'ten' => 'The ten field is required.',
                'ca_hoc' => 'The ca hoc field is required.',
                'giao_vien_id' => 'The giao vien id field is required.',
                'chi_nhanh_id' => 'The chi nhanh id field is required.',
            ])
            ->assertRedirect( route('lop-hoc.edit', $item->id) );
    }

    public function test_lop_hoc_can_be_updated(): void
    {
        $user = User::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $item = LopHoc::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('lop-hoc.edit', $item->id) )
            ->patch( route('lop-hoc.update'), [
                'id' => $item->id,
                'ten' => 'Update Tên',
                'ca_hoc' => 'Update ca hoc',
                'giao_vien_id' => $giaoVien->id,
                'chi_nhanh_id' => $chiNhanh->id,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('lop-hoc.edit', $item->id) );

        $item->refresh();

        $this->assertSame('Update Tên', $item->ten);
        $this->assertSame('Update ca hoc', $item->ca_hoc);
        $this->assertSame($giaoVien->id, $item->giao_vien_id);
        $this->assertSame($chiNhanh->id, $item->chi_nhanh_id);
    }

    public function test_lop_hoc_has_hoc_phi_can_not_be_deleted():void
    {
        $user = User::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $lopHoc = LopHoc::factory()->create();
        $hocVien = HocVien::factory()->create();
        $hocPhi = HocPhi::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('lop-hoc.index') )
            ->post( route('lop-hoc.delete'), [
                'id' => $lopHoc->id,
            ]);

        $response
            ->assertSessionHas('status', 'error')
            ->assertRedirect( route('lop-hoc.index') );

        $this->assertNotSoftDeleted($lopHoc);

    }

    public function test_lop_hoc_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $lopHoc = LopHoc::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('lop-hoc.index') )
            ->post( route('lop-hoc.delete'), [
                'id' => $lopHoc->id
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('lop-hoc.index') );

        $lopHoc->refresh();
        $this->assertSoftDeleted($lopHoc);
    }
}
