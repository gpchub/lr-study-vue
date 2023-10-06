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

class HocPhiTest extends TestCase
{
    use RefreshDatabase;

    public function test_hoc_phi_index_is_displayed(): void
    {
        $response = $this->get( route('hoc-phi.index') );

        $response->assertStatus(200);
    }

    public function test_hoc_phi_edit_is_displayed(): void
    {
        $hocVien = HocVien::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $lopHoc = LopHoc::factory()->create();
        $item = HocPhi::factory()->create();

        $response = $this->get( route('hoc-phi.edit', $item->id) ) ;

        $response->assertStatus(200);
    }

    public function test_hoc_phi_can_be_validated_on_updating(): void
    {
        $user = User::factory()->create();
        $hocVien = HocVien::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $lopHoc = LopHoc::factory()->create();
        $item = HocPhi::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-phi.edit', $item->id) )
            ->patch( route('hoc-phi.update'), []);

        $response
            ->assertSessionHasErrors([
                'so_tien',
                'hoc_vien_id',
                'lop_hoc_id',
            ])
            ->assertRedirect( route('hoc-phi.edit', $item->id) );
    }

    public function test_hoc_phi_can_be_updated(): void
    {
        $user = User::factory()->create();
        $hocVien = HocVien::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $lopHoc = LopHoc::factory()->create();
        $hocPhi = HocPhi::factory()->create();
        $postData = $this->postData($hocVien->id, $lopHoc->id, $hocPhi->id);

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-phi.edit', $hocPhi->id) )
            ->patch( route('hoc-phi.update'), $postData);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('hoc-phi.edit', $hocPhi->id) );

        $hocPhi->refresh();

        $this->assertSame($postData['hoc_vien_id'], $hocPhi->hoc_vien_id);
        $this->assertSame($postData['lop_hoc_id'], $hocPhi->lop_hoc_id);
        $this->assertSame($postData['so_tien'], $hocPhi->so_tien);
        $this->assertSame($postData['ngay_dong'], $hocPhi->ngay_dong->format('Y/m/d'));
    }

    public function test_hoc_phi_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $hocVien = HocVien::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $lopHoc = LopHoc::factory()->create();
        $hocPhi = HocPhi::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('hoc-phi.index') )
            ->post( route('hoc-phi.delete'), [
                'id' => $hocPhi->id
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('hoc-phi.index') );

        $this->assertModelMissing($hocPhi);
    }

    protected function postData($hocVienId, $lopHocId, $id = null)
    {
        $data = [
            'hoc_vien_id' => $hocVienId,
            'lop_hoc_id' => $lopHocId,
            'so_tien' => 100,
            'ngay_dong' => '2023/10/10'
        ];

        if ($id) {
            $data['id'] = $id;
        }

        return $data;
    }
}
