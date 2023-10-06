<?php

namespace Tests\Feature;

use App\Models\ChungChi;
use App\Models\HocVien;
use App\Models\LichThi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LichThiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lich_thi_index_is_displayed(): void
    {
        $response = $this->get( route('lich-thi.index') );

        $response->assertStatus(200);
    }

    public function test_lich_thi_create_is_displayed(): void
    {
        $response = $this->get(route('lich-thi.create'));

        $response->assertStatus(200);
    }

    public function test_lich_thi_can_be_validated_on_creating(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('lich-thi.create'))
            ->post(route('lich-thi.store'), []);

        $response
            ->assertSessionHasErrors(['chung_chi_id', 'ngay_thi', 'dia_diem'])
            ->assertRedirect( route('lich-thi.create') );
    }

    public function test_lich_thi_can_be_created(): void
    {
        $user = User::factory()->create();
        $chungChi = ChungChi::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('lich-thi.store'), [
                'chung_chi_id' => $chungChi->id,
                'ngay_thi' => '2023/10/10 08:00',
                'dia_diem' => 'Địa điểm'
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('lich-thi.index'));

        $item = LichThi::latest()->first();

        $this->assertSame($chungChi->id, $item->chung_chi_id);
        $this->assertSame('2023/10/10 08:00', $item->ngay_thi->format('Y/m/d H:i'));
        $this->assertSame('Địa điểm', $item->dia_diem);
    }

    public function test_lich_thi_edit_is_displayed(): void
    {
        $chungChi = ChungChi::factory()->create();
        $item = LichThi::factory()->create();
        $response = $this->get( route('lich-thi.edit', $item->id) ) ;

        $response->assertStatus(200);
    }

    public function test_lich_thi_can_be_validated_on_updating(): void
    {
        $user = User::factory()->create();
        $chungChi = ChungChi::factory()->create();
        $item = LichThi::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('lich-thi.edit', $item->id) )
            ->patch( route('lich-thi.update'), []);

        $response
            ->assertSessionHasErrors(['chung_chi_id', 'ngay_thi', 'dia_diem'])
            ->assertRedirect( route('lich-thi.edit', $item->id) );
    }

    public function test_lich_thi_can_be_updated(): void
    {
        $user = User::factory()->create();
        $chungChi = ChungChi::factory()->create();
        $item = LichThi::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('lich-thi.edit', $item->id) )
            ->patch( route('lich-thi.update'), [
                'id' => $item->id,
                'ngay_thi' => '2023/10/10 08:00',
                'dia_diem' => 'Địa điểm',
                'chung_chi_id' => $chungChi->id,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('lich-thi.edit', $item->id) );

        $item->refresh();

        $this->assertSame($chungChi->id, $item->chung_chi_id);
        $this->assertSame('2023/10/10 08:00', $item->ngay_thi->format('Y/m/d H:i'));
        $this->assertSame('Địa điểm', $item->dia_diem);
    }

    public function test_lich_thi_has_hoc_vien_can_not_be_deleted():void
    {
        $user = User::factory()->create();
        $chungChi = ChungChi::factory()->create();
        $lichThi = LichThi::factory()->create();
        $hocVien = HocVien::factory()->create();
        $hocVien->lich_thi()->attach($lichThi->id);

        $response = $this
            ->actingAs($user)
            ->from( route('lich-thi.index') )
            ->post( route('lich-thi.delete'), [
                'id' => $lichThi->id,
            ]);

        $response
            ->assertSessionHas('status', 'error')
            ->assertRedirect( route('lich-thi.index') );

        $this->assertModelExists($lichThi);
    }

    public function test_lich_thi_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $chungChi = ChungChi::factory()->create();
        $lichThi = LichThi::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('lich-thi.index') )
            ->post( route('lich-thi.delete'), [
                'id' => $lichThi->id
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('lich-thi.index') );

        $this->assertModelMissing($lichThi);
    }
}
