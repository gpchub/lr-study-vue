<?php

namespace Tests\Feature;

use App\Models\ChiNhanh;
use App\Models\GiaoVien;
use App\Models\LopHoc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ChiNhanhTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        ChiNhanh::factory()->create([
            'ten' => 'XYZ',
            'email' => 'cn1@example.test',
        ]);

        ChiNhanh::factory()->create([
            'ten' => 'ABC',
            'email' => 'cn2@example.test',
        ]);
    }

    public function test_chi_nhanh_index_is_displayed(): void
    {
        $response = $this->get(route('chi-nhanh.index'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->has('list')
            ->has('list.data', 2)
            ->has('list.data.0', fn (Assert $assert) => $assert
                ->where('id', 2)
                ->where('ten', 'ABC')
                ->where('email', 'cn2@example.test')
                ->where('deleted_at', null)
                ->etc()
            )
            ->has('list.data.1', fn (Assert $assert) => $assert
                ->where('id', 1)
                ->where('ten', 'XYZ')
                ->where('email', 'cn1@example.test')
                ->where('deleted_at', null)
                ->etc()
            )
        );
    }

    public function test_chi_nhanh_create_is_displayed(): void
    {
        $response = $this->get(route('chi-nhanh.create'));

        $response->assertStatus(200);
    }

    /**
     * @dataProvider invalidPostData
     */
    public function test_chi_nhanh_can_be_validated_on_creating($invalidData, $invalidFields, $validFields): void
    {
        $response = $this
            ->actingAs($this->user)
            ->from(route('chi-nhanh.create'))
            ->post(route('chi-nhanh.store'), $invalidData);

        $response
            ->assertValid($validFields)
            ->assertInvalid($invalidFields)
            ->assertRedirect(route('chi-nhanh.create'));
    }

    public function test_chi_nhanh_can_be_created(): void
    {
        $postData = $this->postData();
        $response = $this
            ->actingAs($this->user)
            ->post('/chi-nhanh/store', $this->postData());

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/chi-nhanh');

        $item = ChiNhanh::latest('id')->first();

        $this->assertSame($postData['ten'], $item->ten);
        $this->assertSame($postData['email'], $item->email);
        $this->assertSame($postData['dia_chi'], $item->dia_chi);
        $this->assertSame($postData['dien_thoai'], $item->dien_thoai);
    }

    public function test_chi_nhanh_edit_is_displayed(): void
    {
        $chiNhanh = ChiNhanh::first();
        $this->createLopHocForChiNhanh($chiNhanh, 3);

        $response = $this
            ->get('/chi-nhanh/edit/' . $chiNhanh->id)
            ->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->has('chiNhanh', fn (Assert $assert) => $assert
                ->where('ten', 'XYZ')
                ->etc()
            )
            ->has('danhSachLop', 3)
        );
    }

    /**
     * @dataProvider invalidPostData
     */
    public function test_chi_nhanh_can_be_validated_on_updating($invalidData, $invalidFields, $validFields): void
    {
        $chiNhanh = ChiNhanh::first();

        $response = $this
            ->actingAs($this->user)
            ->from('/chi-nhanh/edit/' . $chiNhanh->id)
            ->patch('/chi-nhanh/update', $invalidData);

        $response
            ->assertValid($validFields)
            ->assertInvalid($invalidFields)
            ->assertRedirect('/chi-nhanh/edit/' . $chiNhanh->id);
    }

    public function test_chi_nhanh_can_be_updated(): void
    {
        $chiNhanh = ChiNhanh::first();

        $postData = $this->postData($chiNhanh->id);
        $response = $this
            ->actingAs($this->user)
            ->from('/chi-nhanh/edit/' . $chiNhanh->id)
            ->patch('/chi-nhanh/update', $postData);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/chi-nhanh/edit/' . $chiNhanh->id);

        $chiNhanh->refresh();

        $this->assertSame($postData['ten'], $chiNhanh->ten);
        $this->assertSame($postData['email'], $chiNhanh->email);
        $this->assertSame($postData['dia_chi'], $chiNhanh->dia_chi);
        $this->assertSame($postData['dien_thoai'], $chiNhanh->dien_thoai);
    }

    public function test_chi_nhanh_can_be_deleted(): void
    {
        $chiNhanh = ChiNhanh::first();

        $response = $this
            ->actingAs($this->user)
            ->from('/chi-nhanh')
            ->post('/chi-nhanh/delete', [
                'id' => $chiNhanh->id
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/chi-nhanh');

        $this->assertSoftDeleted($chiNhanh);
    }

    public function test_chi_nhanh_has_lop_hoc_can_not_be_deleted():void
    {
        $chiNhanh = ChiNhanh::first();
        $this->createLopHocForChiNhanh($chiNhanh, 1);

        $response = $this
            ->actingAs($this->user)
            ->from('/chi-nhanh')
            ->post('/chi-nhanh/delete', [
                'id' => $chiNhanh->id,
            ]);

        $response
            ->assertSessionHas('status', 'error')
            ->assertRedirect('/chi-nhanh');

        $this->assertNotSoftDeleted($chiNhanh);
    }

    protected function createLopHocForChiNhanh(ChiNhanh $chiNhanh, $count = 3)
    {
        $giaoVien = GiaoVien::factory()->create();
        $lopHoc = LopHoc::factory($count)->create([
            'chi_nhanh_id' => $chiNhanh->id,
        ]);
    }

    protected function postData($id = null)
    {
        $data = [
            'ten' => 'Chi nhánh ABC',
            'email' => 'test@example.com',
            'dia_chi' => 'Nha khong so duong khong ten',
            'dien_thoai' => '0912345678',
        ];

        if ($id) {
            $data['id'] = $id;
        }

        return $data;
    }

    public static function invalidPostData()
    {
        return [
            [
                [],
                ['ten', 'email', 'dia_chi', 'dien_thoai'],
                []
            ],
            [
                ['ten' => 'CN1', 'email' => 'email', 'dia_chi' => 'abc', 'dien_thoai' => 'abc'],
                ['email'],
                ['ten'],
            ],
        ];
    }
}
