<?php

namespace Tests\Feature;

use App\Models\ChungChi;
use App\Models\LichThi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class ChungChiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        ChungChi::factory()->create([
            'ten' => 'XYZ',
        ]);

        ChungChi::factory()->create([
            'ten' => 'ABC',
        ]);
    }

    public function test_chung_chi_index_is_displayed(): void
    {
        $response = $this->get( route('chung-chi.index') );

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->has('list')
            ->has('list.data', 2)
            ->has('list.data.0', fn (Assert $assert) => $assert
                ->where('id', 1)
                ->where('ten', 'XYZ')
                ->etc()
            )
            ->has('list.data.1', fn (Assert $assert) => $assert
                ->where('id', 2)
                ->where('ten', 'ABC')
                ->etc()
            )
        );
    }

    public function test_chung_chi_create_is_displayed(): void
    {
        $response = $this->get(route('chung-chi.create'));

        $response->assertStatus(200);
    }

    public function test_chung_chi_can_be_validated_on_creating(): void
    {
        $response = $this
            ->actingAs($this->user)
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
        $response = $this
            ->actingAs($this->user)
            ->post(route('chung-chi.store'), [
                'ten' => 'Tên',
                'mo_ta' => 'Mô tả',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('chung-chi.index'));

        $item = ChungChi::latest('id')->first();

        $this->assertSame('Tên', $item->ten);
        $this->assertSame('Mô tả', $item->mo_ta);
    }

    public function test_chung_chi_edit_is_displayed(): void
    {
        $item = ChungChi::first();
        $this->createLichThiForChungChi($item, 3);

        $response = $this
            ->get( route('chung-chi.edit', $item->id) )
            ->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->has('chungChi',fn (Assert $assert) => $assert
                ->where('ten', 'XYZ')
                ->etc()
            )
            ->has('lichThi.data', 3)
        );
    }

    public function test_chung_chi_can_be_validated_on_updating(): void
    {
        $item = ChungChi::first();

        $response = $this
            ->actingAs($this->user)
            ->from( route('chung-chi.edit', $item->id) )
            ->patch( route('chung-chi.update'), []);

        $response
            ->assertSessionHasErrors(['ten'])
            ->assertRedirect( route('chung-chi.edit', $item->id) );
    }

    public function test_chung_chi_can_be_updated(): void
    {
        $item = ChungChi::first();

        $response = $this
            ->actingAs($this->user)
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
        $chungChi = ChungChi::first();
        $this->createLichThiForChungChi($chungChi);

        $response = $this
            ->actingAs($this->user)
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
        $chungChi = ChungChi::first();

        $response = $this
            ->actingAs($this->user)
            ->from( route('chung-chi.index') )
            ->post( route('chung-chi.delete'), [
                'id' => $chungChi->id
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('chung-chi.index') );

        $this->assertModelMissing($chungChi);
    }

    protected function createLichThiForChungChi(ChungChi $chungChi, $count = 1)
    {
        $lichThi = LichThi::factory($count)->create([
            'chung_chi_id' => $chungChi->id,
        ]);
    }
}
