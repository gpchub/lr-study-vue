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
use Inertia\Testing\AssertableInertia as Assert;

class GiaoVienTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        GiaoVien::factory()->create([
            'ho' => 'Nguyen',
            'ten' => 'XYZ',
            'email' => 'gv1@example.test',
            'dien_thoai' => '123',
        ]);

        GiaoVien::factory()->create([
            'ho' => 'Tran',
            'ten' => 'ABC',
            'email' => 'gv2@example.test',
            'dien_thoai' => '456',
        ]);

        GiaoVien::factory()->create([
            'ho' => 'Le',
            'ten' => 'ABD',
            'email' => 'gv3@example.test',
            'dien_thoai' => '789',
        ]);
    }

    public function test_giao_vien_index_is_displayed(): void
    {
        $response = $this->get(route('giao-vien.index'));

        $response->assertStatus(200);

        $response->assertInertia(
            fn (Assert $page) => $page
                ->has('list')
                ->has('list.data', 3)
                ->has(
                    'list.data.0',
                    fn (Assert $assert) => $assert
                        ->where('ho_va_ten', 'Le ABD')
                        ->etc()
                )
                ->has(
                    'list.data.1',
                    fn (Assert $assert) => $assert
                        ->where('ho_va_ten', 'Tran ABC')
                        ->etc()
                )
                ->has(
                    'list.data.2',
                    fn (Assert $assert) => $assert
                        ->where('ho_va_ten', 'Nguyen XYZ')
                        ->etc()
                )
        );
    }

    /**
     * @dataProvider searchData
     */
    public function test_giao_vien_can_be_filter($field, $value, $count, $first): void
    {
        $response = $this->get(route('giao-vien.index', [
            'filter' => [
                $field => $value
            ],
        ]));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) =>
            $page
                ->has('list')
                ->has('list.data', $count)
                ->where('filter.' . $field, $value)
                ->has(
                    'list.data.0',
                    fn (Assert $assert) => $assert
                        ->where('ho_va_ten', $first)
                        ->etc()
                )
        );
    }

    /**
     * @dataProvider sortData
     */
    public function test_giao_vien_can_be_sort($sort, $first): void
    {
        $response = $this->get(route('giao-vien.index', [
           'sort' => $sort
        ]));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
                ->has('list')
                ->has('list.data', 3)
                ->where('sort', $sort)
                ->has(
                    'list.data.0', fn (Assert $assert) => $assert
                        ->where('ho_va_ten', $first)
                        ->etc()
                )
        );
    }

    public function test_giao_vien_create_is_displayed(): void
    {
        $response = $this->get(route('giao-vien.create'));

        $response->assertStatus(200);
    }

    /**
     * @dataProvider invalidPostData
     */
    public function test_giao_vien_can_be_validated_on_creating($data, $invalidFields, $validFields): void
    {
        $response = $this
            ->actingAs($this->user)
            ->from(route('giao-vien.create'))
            ->post(route('giao-vien.store'), $data);

        $response
            ->assertValid($validFields)
            ->assertInvalid($invalidFields)
            ->assertRedirect(route('giao-vien.create'));
    }

    public function test_giao_vien_can_be_created(): void
    {
        $postData = $this->postData();
        $response = $this
            ->actingAs($this->user)
            ->post(route('giao-vien.store'), $postData);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('giao-vien.index'));

        $item = GiaoVien::latest('id')->first();

        $this->assertSame($postData['ho'], $item->ho);
        $this->assertSame($postData['ten'], $item->ten);
        $this->assertSame($postData['email'], $item->email);
        $this->assertSame($postData['dien_thoai'], $item->dien_thoai);
        $this->assertSame($postData['ngay_sinh'], $item->ngay_sinh->format('Y-m-d'));
        $this->assertSame($postData['gioi_tinh'], $item->gioi_tinh);
    }

    public function test_giao_vien_edit_is_displayed(): void
    {
        $giaoVien = GiaoVien::first();
        $this->createLopHocForGiaoVien($giaoVien, 3);

        $response = $this->get(route('giao-vien.edit', $giaoVien->id));
        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->has('giaoVien', fn (Assert $assert) => $assert
                ->where('ten', 'XYZ')
                ->etc()
            )
            ->has('listLopHoc.data', 3)
        );
    }

    /**
     * @dataProvider invalidPostData
     */
    public function test_giao_vien_can_be_validated_on_updating($data, $invalidFields, $validFields): void
    {
        $giaoVien = GiaoVien::first();

        $response = $this
            ->actingAs($this->user)
            ->from(route('giao-vien.edit', $giaoVien->id))
            ->patch(route('giao-vien.update'), $data);

        $response
            ->assertValid($validFields)
            ->assertInvalid($invalidFields)
            ->assertRedirect(route('giao-vien.edit', $giaoVien->id));
    }

    public function test_giao_vien_can_be_updated(): void
    {
        $giaoVien = GiaoVien::first();

        $postData = $this->postData($giaoVien->id);
        $response = $this
            ->actingAs($this->user)
            ->from(route('giao-vien.edit', $giaoVien->id))
            ->patch(route('giao-vien.update'), $postData);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('giao-vien.edit', $giaoVien->id));

        $giaoVien->refresh();

        $this->assertSame($postData['ho'], $giaoVien->ho);
        $this->assertSame($postData['ten'], $giaoVien->ten);
        $this->assertSame($postData['email'], $giaoVien->email);
        $this->assertSame($postData['dien_thoai'], $giaoVien->dien_thoai);
        $this->assertSame($postData['ngay_sinh'], $giaoVien->ngay_sinh->format('Y-m-d'));
        $this->assertSame($postData['gioi_tinh'], $giaoVien->gioi_tinh);
    }

    public function test_giao_vien_has_lop_hoc_can_not_be_deleted(): void
    {
        $giaoVien = GiaoVien::first();
        $this->createLopHocForGiaoVien($giaoVien);

        $response = $this
            ->actingAs($this->user)
            ->from(route('giao-vien.index'))
            ->post(route('giao-vien.delete'), [
                'id' => $giaoVien->id,
            ]);

        $response
            ->assertSessionHas('status', 'error')
            ->assertRedirect(route('giao-vien.index'));

        $this->assertNotSoftDeleted($giaoVien);
    }

    public function test_giao_vien_can_be_deleted(): void
    {
        $giaoVien = GiaoVien::first();

        $response = $this
            ->actingAs($this->user)
            ->from(route('giao-vien.index'))
            ->post(route('giao-vien.delete'), [
                'id' => $giaoVien->id
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('giao-vien.index'));

        $this->assertSoftDeleted($giaoVien);
    }

    public static function searchData()
    {
        yield 'filter by ten' => [ 'ten', 'AB', 2, 'Le ABD' ];
        yield 'filter by ho' => [ 'ten', 'Nguyen', 1, 'Nguyen XYZ' ];
        yield 'filter by email' => [ 'email', 'gv3', 1, 'Le ABD' ];
        yield 'filter by dien_thoai' => [ 'dien_thoai', '123', 1, 'Nguyen XYZ' ];
    }

    public static function sortData()
    {
        yield 'sort by ten asc' => [ 'ten', 'Tran ABC' ];
        yield 'sort by ten desc' => [ '-ten', 'Nguyen XYZ' ];
        yield 'sort by id asc' => [ 'id', 'Nguyen XYZ' ];
        yield 'sort by id desc' => [ '-id', 'Le ABD' ];
    }

    public static function invalidPostData()
    {
        return [
            [
                ['ho' => 'Dinh', 'ten' => 'Ly'],
                ['email', 'dien_thoai', 'ngay_sinh'],
                ['ho', 'ten']
            ],
            [
                ['ho' => 'Dinh', 'ten' => 'Ly', 'email' => 'email', 'dien_thoai' => '123', 'ngay_sinh' => '2000-01-03'],
                ['email',],
                ['ho', 'ten', 'dien_thoai', 'ngay_sinh']
            ],
        ];
    }

    public function postData($id = null)
    {
        $data = [
            'ho' => 'Dinh',
            'ten' => 'Ly',
            'email' => 'test@example.com',
            'dien_thoai' => '0912345699',
            'ngay_sinh' => '2000-02-02',
            'gioi_tinh' => 2,
        ];

        if ($id) {
            $data['id'] = $id;
        }

        return $data;
    }

    public function createLopHocForGiaoVien(GiaoVien $giaoVien, $count = 1)
    {
        ChiNhanh::factory()->create();
        LopHoc::factory($count)->create([
            'giao_vien_id' => $giaoVien->id,
        ]);
    }
}
