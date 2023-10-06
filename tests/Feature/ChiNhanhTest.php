<?php

namespace Tests\Feature;

use App\Models\ChiNhanh;
use App\Models\GiaoVien;
use App\Models\LopHoc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChiNhanhTest extends TestCase
{
    use RefreshDatabase;

    public function test_chi_nhanh_index_is_displayed(): void
    {
        $response = $this->get('/chi-nhanh');

        $response->assertStatus(200);
    }

    public function test_chi_nhanh_create_is_displayed(): void
    {
        $response = $this->get('/chi-nhanh/create');

        $response->assertStatus(200);
    }

    public function test_chi_nhanh_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/chi-nhanh/store', [
                'ten' => 'Test Chi nhanh',
                'email' => 'test@example.com',
                'dia_chi' => 'Nha khong so duong khong ten',
                'dien_thoai' => '0912345678',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/chi-nhanh');

        $item = ChiNhanh::latest()->first();

        $this->assertSame('Test Chi nhanh', $item->ten);
        $this->assertSame('test@example.com', $item->email);
        $this->assertSame('Nha khong so duong khong ten', $item->dia_chi);
        $this->assertSame('0912345678', $item->dien_thoai);
    }

    public function test_chi_nhanh_can_be_validated_on_creating(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/chi-nhanh/create')
            ->post('/chi-nhanh/store', [
                'ten' => '',
                'email' => 'abc',
            ]);

        $response
            ->assertSessionHasErrors([
                'ten' => 'The ten field is required.',
                'email' => 'The email field must be a valid email address.',
                'dia_chi' => 'The dia chi field is required.',
                'dien_thoai' => 'The dien thoai field is required.',
            ])
            ->assertRedirect('/chi-nhanh/create');
    }

    public function test_chi_nhanh_edit_is_displayed(): void
    {
        $chiNhanh = ChiNhanh::factory()->create();
        $response = $this->get('/chi-nhanh/edit/' . $chiNhanh->id);

        $response->assertStatus(200);
    }

    public function test_chi_nhanh_can_be_validated_on_updating(): void
    {
        $user = User::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/chi-nhanh/edit/' . $chiNhanh->id)
            ->patch('/chi-nhanh/update', [
                'ten' => '',
                'email' => 'abc',
            ]);

        $response
            ->assertSessionHasErrors([
                'ten' => 'The ten field is required.',
                'email' => 'The email field must be a valid email address.',
                'dia_chi' => 'The dia chi field is required.',
                'dien_thoai' => 'The dien thoai field is required.',
            ])
            ->assertRedirect('/chi-nhanh/edit/' . $chiNhanh->id);
    }

    public function test_chi_nhanh_can_be_updated(): void
    {
        $user = User::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/chi-nhanh/edit/' . $chiNhanh->id)
            ->patch('/chi-nhanh/update', [
                'id' => $chiNhanh->id,
                'ten' => 'Update Chi nhanh',
                'email' => 'update@example.com',
                'dia_chi' => 'Update Nha khong so duong khong ten',
                'dien_thoai' => '0912345699',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/chi-nhanh/edit/' . $chiNhanh->id);

        $chiNhanh->refresh();

        $this->assertSame('Update Chi nhanh', $chiNhanh->ten);
        $this->assertSame('update@example.com', $chiNhanh->email);
        $this->assertSame('Update Nha khong so duong khong ten', $chiNhanh->dia_chi);
        $this->assertSame('0912345699', $chiNhanh->dien_thoai);
    }

    public function test_chi_nhanh_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $chiNhanh = ChiNhanh::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/chi-nhanh')
            ->post('/chi-nhanh/delete', [
                'id' => $chiNhanh->id
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/chi-nhanh');

        $chiNhanh->refresh();
        $this->assertSoftDeleted($chiNhanh);
    }

    public function test_chi_nhanh_has_lop_hoc_can_not_be_deleted():void
    {
        $user = User::factory()->create();
        $item = ChiNhanh::factory()->create();
        $giaoVien = GiaoVien::factory()->create();
        $lopHoc = LopHoc::factory()->create([
            'chi_nhanh_id' => $item->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/chi-nhanh')
            ->post('/chi-nhanh/delete', [
                'id' => $item->id,
            ]);

        $response
            ->assertSessionHas('status', 'error')
            ->assertRedirect('/chi-nhanh');

        $this->assertNotSoftDeleted($item);

    }
}
