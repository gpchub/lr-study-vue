<?php

namespace Tests\Feature;

use App\Models\ChungChi;
use App\Models\LichThi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChungChiTest extends TestCase
{
    use RefreshDatabase;

    public function test_chung_chi_index_is_displayed(): void
    {
        $response = $this->get( route('chung-chi.index') );

        $response->assertStatus(200);
    }

    public function test_chung_chi_create_is_displayed(): void
    {
        $response = $this->get(route('chung-chi.create'));

        $response->assertStatus(200);
    }

    public function test_chung_chi_can_be_validated_on_creating(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('chung-chi.create'))
            ->post(route('chung-chi.store'), [
                'ten' => '',
            ]);

        $response
            ->assertSessionHasErrors(['ten'])
            ->assertRedirect( route('chung-chi.create') );
    }

    public function test_chung_chi_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('chung-chi.store'), [
                'ten' => 'Tên',
                'mo_ta' => 'Mô tả',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('chung-chi.index'));

        $item = ChungChi::latest()->first();

        $this->assertSame('Tên', $item->ten);
        $this->assertSame('Mô tả', $item->mo_ta);
    }

    public function test_chung_chi_edit_is_displayed(): void
    {
        $item = ChungChi::factory()->create();
        $response = $this->get( route('chung-chi.edit', $item->id) ) ;

        $response->assertStatus(200);
    }

    public function test_chung_chi_can_be_validated_on_updating(): void
    {
        $user = User::factory()->create();
        $item = ChungChi::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('chung-chi.edit', $item->id) )
            ->patch( route('chung-chi.update'), [
                'ten' => '',
            ]);

        $response
            ->assertSessionHasErrors(['ten'])
            ->assertRedirect( route('chung-chi.edit', $item->id) );
    }

    public function test_chung_chi_can_be_updated(): void
    {
        $user = User::factory()->create();
        $item = ChungChi::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('chung-chi.edit', $item->id) )
            ->patch( route('chung-chi.update'), [
                'id' => $item->id,
                'ten' => 'Update Tên',
                'mo_ta' => 'Update Mô tả',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('chung-chi.edit', $item->id) );

        $item->refresh();

        $this->assertSame('Update Tên', $item->ten);
        $this->assertSame('Update Mô tả', $item->mo_ta);
    }

    public function test_chung_chi_has_lich_thi_can_not_be_deleted():void
    {
        $user = User::factory()->create();
        $chungChi = ChungChi::factory()->create();
        $lichThi = LichThi::factory()->create([
            'chung_chi_id' => $chungChi->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->from( route('chung-chi.index') )
            ->post( route('chung-chi.delete'), [
                'id' => $chungChi->id,
            ]);

        $response
            ->assertSessionHas('status', 'error')
            ->assertRedirect( route('chung-chi.index') );

        $this->assertModelExists($chungChi);
    }

    public function test_chung_chi_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $chungChi = ChungChi::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from( route('chung-chi.index') )
            ->post( route('chung-chi.delete'), [
                'id' => $chungChi->id
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('chung-chi.index') );

        $this->assertModelMissing($chungChi);
    }
}
