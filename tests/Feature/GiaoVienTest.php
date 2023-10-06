<?php

namespace Tests\Feature;

use App\Models\ChiNhanh;
use App\Models\GiaoVien;
use App\Models\LopHoc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GiaoVienTest extends TestCase
{
    use RefreshDatabase;

    public function test_giao_vien_index_is_displayed(): void
    {
        $response = $this->get(route('giao-vien.index'));

        $response->assertStatus(200);
    }

    public function test_giao_vien_create_is_displayed(): void
    {
        $response = $this->get(route('giao-vien.create'));

        $response->assertStatus(200);
    }

    public function test_giao_vien_can_be_validated_on_creating(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('giao-vien.create'))
            ->post(route('giao-vien.store'), [
                'ho' => '',
                'ten' => '',
                'email' => 'email',
            ]);

        $response
            ->assertSessionHasErrors([
                'ho' => 'The ho field is required.',
                'ten' => 'The ten field is required.',
                'email' => 'The email field must be a valid email address.',
                'dien_thoai' => 'The dien thoai field is required.',
                'ngay_sinh' => 'The ngay sinh field is required.',
            ])
            ->assertRedirect( route('giao-vien.create') );
    }

    public function test_giao_vien_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('giao-vien.store'), [
                'ho' => 'Họ và chữ lót',
                'ten' => 'Tên',
                'email' => 'test@example.com',
                'dien_thoai' => '0912345678',
                'ngay_sinh' => '2000/01/01',
                'gioi_tinh' => 1,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('giao-vien.index'));

        $item = GiaoVien::latest()->first();

        $this->assertSame('Họ và chữ lót', $item->ho);
        $this->assertSame('Tên', $item->ten);
        $this->assertSame('test@example.com', $item->email);
        $this->assertSame('0912345678', $item->dien_thoai);
        $this->assertSame('2000/01/01', $item->ngay_sinh->format('Y/m/d'));
        $this->assertSame(1, $item->gioi_tinh);
    }

    public function test_giao_vien_edit_is_displayed(): void
    {
        $giaoVien = GiaoVien::factory()->create();
        $response = $this->get( route('giao-vien.edit', $giaoVien->id) ) ;

        $response->assertStatus(200);
    }

    public function test_giao_vien_can_be_validated_on_updating(): void
    {
        $user = User::factory()->create();
        $giaoVien = GiaoVien::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('giao-vien.edit', $giaoVien->id) )
            ->patch( route('giao-vien.update'), [
                'ho' => '',
                'ten' => '',
                'email' => 'email',
            ]);

        $response
            ->assertSessionHasErrors([
                'ho' => 'The ho field is required.',
                'ten' => 'The ten field is required.',
                'email' => 'The email field must be a valid email address.',
                'dien_thoai' => 'The dien thoai field is required.',
                'ngay_sinh' => 'The ngay sinh field is required.',
            ])
            ->assertRedirect( route('giao-vien.edit', $giaoVien->id) );
    }

    public function test_giao_vien_can_be_updated(): void
    {
        $user = User::factory()->create();
        $giaoVien = GiaoVien::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('giao-vien.edit', $giaoVien->id) )
            ->patch( route('giao-vien.update'), [
                'id' => $giaoVien->id,
                'ho' => 'Update Họ và chữ lót',
                'ten' => 'Update Tên',
                'email' => 'update.test@example.com',
                'dien_thoai' => '0912345699',
                'ngay_sinh' => '2000/02/02',
                'gioi_tinh' => 2,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('giao-vien.edit', $giaoVien->id) );

        $giaoVien->refresh();

        $this->assertSame('Update Họ và chữ lót', $giaoVien->ho);
        $this->assertSame('Update Tên', $giaoVien->ten);
        $this->assertSame('update.test@example.com', $giaoVien->email);
        $this->assertSame('0912345699', $giaoVien->dien_thoai);
        $this->assertSame('2000/02/02', $giaoVien->ngay_sinh->format('Y/m/d'));
        $this->assertSame(2, $giaoVien->gioi_tinh);
    }

    public function test_giao_vien_has_lop_hoc_can_not_be_deleted():void
    {
        $user = User::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $lopHoc = LopHoc::factory()->create([
            'giao_vien_id' => $giaoVien->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->from( route('giao-vien.index') )
            ->post( route('giao-vien.delete'), [
                'id' => $giaoVien->id,
            ]);

        $response
            ->assertSessionHas('status', 'error')
            ->assertRedirect( route('giao-vien.index') );

        $this->assertNotSoftDeleted($giaoVien);

    }

    public function test_giao_vien_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $giaoVien = GiaoVien::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('giao-vien.index') )
            ->post( route('giao-vien.delete'), [
                'id' => $giaoVien->id
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('giao-vien.index') );

        $giaoVien->refresh();
        $this->assertSoftDeleted($giaoVien);
    }
}
