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
use Inertia\Testing\AssertableInertia as Assert;

class HocVienTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        GiaoVien::factory(3)->create();
        ChiNhanh::factory(3)->create();
        $lop1 = LopHoc::factory()->create(['id' => 1]);
        $lop2 = LopHoc::factory()->create(['id' => 2]);
        $lop3 = LopHoc::factory()->create(['id' => 3]);

        HocVien::factory()->create([
            'id' => 1,
            'ho' => 'Nguyen',
            'ten' => 'XYZ',
            'email' => 'hv1@example.test',
            'dien_thoai' => '123',
        ])->lop_hoc()->attach($lop1->id, ['ngay_bat_dau' => '2023-10-10']);

        HocVien::factory()->create([
            'id' => 2,
            'ho' => 'Tran',
            'ten' => 'ABC',
            'email' => 'hv2@example.test',
            'dien_thoai' => '456',
        ])->lop_hoc()->attach($lop2->id, ['ngay_bat_dau' => '2023-10-10']);

        HocVien::factory()->create([
            'id' => 3,
            'ho' => 'Le',
            'ten' => 'ABD',
            'email' => 'hv3@example.test',
            'dien_thoai' => '789',
        ])->lop_hoc()->attach($lop3->id, ['ngay_bat_dau' => '2023-10-10']);
    }

    public function test_hoc_vien_index_is_displayed(): void
    {
        $response = $this->get( route('hoc-vien.index') );

        $response->assertStatus(200);

        $response->assertInertia(
            fn (Assert $page) => $page
                ->has('list')
                ->has('list.data', 3)
                ->has( 'list.data.0', fn (Assert $assert) => $assert ->where('ho_va_ten', 'Le ABD') ->etc() )
                ->has( 'list.data.1', fn (Assert $assert) => $assert ->where('ho_va_ten', 'Tran ABC') ->etc() )
                ->has( 'list.data.2', fn (Assert $assert) => $assert ->where('ho_va_ten', 'Nguyen XYZ') ->etc() )
                ->has( 'listLopHoc', 3 )
        );
    }

    /**
     * @dataProvider searchData
     */
    public function test_hoc_vien_can_be_filter($field, $value, $count, $first): void
    {
        $response = $this->get(route('hoc-vien.index', [
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
                ->has( 'list.data.0', fn (Assert $assert) => $assert ->where('ho_va_ten', $first)->etc() )
        );
    }

    /**
     * @dataProvider sortData
     */
    public function test_giao_vien_can_be_sort($sort, $first): void
    {
        $response = $this->get(route('hoc-vien.index', [
           'sort' => $sort
        ]));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
                ->has('list')
                ->has('list.data', 3)
                ->where('sort', $sort)
                ->has( 'list.data.0', fn (Assert $assert) => $assert ->where('ho_va_ten', $first)->etc() )
        );
    }


    public function test_hoc_vien_create_is_displayed(): void
    {
        $response = $this->get(route('hoc-vien.create'));

        $response->assertStatus(200);
    }

    /**
     * @dataProvider invalidPostData
     */
    public function test_hoc_vien_can_be_validated_on_creating($data, $invalidFields, $validFields): void
    {
        $response = $this
            ->actingAs($this->user)
            ->from(route('hoc-vien.create'))
            ->post(route('hoc-vien.store'), $data);

        $response
            ->assertValid($validFields)
            ->assertInvalid($invalidFields)
            ->assertRedirect( route('hoc-vien.create') );
    }

    public function test_hoc_vien_can_be_created(): void
    {
        $postData = $this->postData();

        $response = $this
            ->actingAs($this->user)
            ->post(route('hoc-vien.store'), $postData);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('hoc-vien.index'));

        $item = HocVien::latest('id')->first();

        $this->assertSame($postData['ho'], $item->ho);
        $this->assertSame($postData['ten'], $item->ten);
        $this->assertSame($postData['email'], $item->email);
        $this->assertSame($postData['dien_thoai'], $item->dien_thoai);
        $this->assertSame($postData['gioi_tinh'], $item->gioi_tinh);
        $this->assertSame($postData['ngay_sinh'], $item->ngay_sinh->format('Y-m-d'));
    }

    public function test_hoc_vien_edit_is_displayed(): void
    {
        $item = HocVien::first();
        $this->createLichThiForHocVien($item, 3);
        $response = $this
            ->get( route('hoc-vien.edit', $item->id) )
            ->assertStatus(200);

        $response->assertInertia(fn (Assert $page) =>
            $page->has('hocVien', fn (Assert $assert) => $assert ->where('ten', 'XYZ') ->etc() )
                ->has('listLopHoc', 3)
                ->has('listLopHocVien', 1)
                ->has('listLopHocVienIds', 1)
                ->has('listLichThi', 3)
                ->has('listLichThiHocVien', 3)
                ->has('listLichThiHocVienIds', 3)
        );
    }

    /**
     * @dataProvider invalidPostData
     */
    public function test_hoc_vien_can_be_validated_on_updating($data, $invalidFields, $validFields): void
    {
        $item = HocVien::first();

        $response = $this
            ->actingAs($this->user)
            ->from( route('hoc-vien.edit', $item->id) )
            ->patch( route('hoc-vien.update'), $data);

        $response
            ->assertValid($validFields)
            ->assertInvalid($invalidFields)
            ->assertRedirect( route('hoc-vien.edit', $item->id) );
    }

    public function test_hoc_vien_can_be_updated(): void
    {
        $item = HocVien::first();
        $postData = $this->postData($item->id);

        $response = $this
            ->actingAs($this->user)
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
        $this->assertSame($postData['ngay_sinh'], $item->ngay_sinh->format('Y-m-d'));
    }

    public function test_hoc_vien_has_hoc_phi_can_not_be_deleted():void
    {
        $hocVien = HocVien::first();
        $this->createHocPhiForHocVien($hocVien);

        $response = $this
            ->actingAs($this->user)
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
        $item = HocVien::first();

        $response = $this
            ->actingAs($this->user)
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
        $item = HocVien::first();

        $response = $this
            ->actingAs($this->user)
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
        $lopHoc = LopHoc::factory()->create();
        $item = HocVien::first();

        $postData = [
            'hoc_vien_id' => $item->id,
            'lop_hoc_id' => $lopHoc->id,
            'ngay_bat_dau' => '2023/10/10',
        ];

        $response = $this
            ->actingAs($this->user)
            ->from( route('hoc-vien.edit', $item->id) )
            ->post( route('hoc-vien.dang-ky-lop'), $postData);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect( route('hoc-vien.edit', $item->id) );

        $lopHocVien = LopHocVien::latest('id')->first();

        $this->assertSame($postData['hoc_vien_id'], $lopHocVien->hoc_vien_id);
        $this->assertSame($postData['lop_hoc_id'], $lopHocVien->lop_hoc_id);
        $this->assertSame($postData['ngay_bat_dau'], $lopHocVien->ngay_bat_dau->format('Y/m/d'));
    }

    public function test_hoc_vien_has_hoc_phi_can_not_xoa_lop(): void
    {
        $hocVien = HocVien::first();
        $lop_hoc = $hocVien->lop_hoc->first();
        HocPhi::factory()->create([
            'hoc_vien_id' => $hocVien->id,
            'lop_hoc_id' => $lop_hoc->id,
        ]);

        $lopHocVien = LopHocVien::where('hoc_vien_id', $hocVien->id)
                            ->where('lop_hoc_id', $lop_hoc->id)
                            ->first();

        $response = $this
            ->actingAs($this->user)
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
        $hocVien = HocVien::first();
        $lop_hoc = $hocVien->lop_hoc->first();
        $lopHocVien = LopHocVien::where('hoc_vien_id', $hocVien->id)
                            ->where('lop_hoc_id', $lop_hoc->id)
                            ->first();

        $response = $this
            ->actingAs($this->user)
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
        $hocVien = HocVien::first();
        $lopHoc = $hocVien->lop_hoc->first();

        /*----------  Test validation  ----------*/
        $response = $this
            ->actingAs($this->user)
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
            ->actingAs($this->user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.dong-hoc-phi'), $postData);

        $hocPhi = HocPhi::latest('id')->first();

        $this->assertSame($postData['hoc_vien_id'], $hocPhi->hoc_vien_id);
        $this->assertSame($postData['lop_hoc_id'], $hocPhi->lop_hoc_id);
        $this->assertSame($postData['so_tien'], $hocPhi->so_tien);
        $this->assertSame($postData['thang'], $hocPhi->thang);
        $this->assertSame($postData['nam'], $hocPhi->nam);
        $this->assertSame($postData['ngay_dong'], $hocPhi->ngay_dong->format('Y/m/d'));

        /*----------  Test dong hoc phi lop da dong hoc phi  ----------*/
        $response = $this
            ->actingAs($this->user)
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
        $hocVien = HocVien::first();
        $chungChi = ChungChi::factory()->create();
        $lichThi = LichThi::factory()->create();

        /*----------  Test validation  ----------*/
        $response = $this
            ->actingAs($this->user)
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
            ->actingAs($this->user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.dang-ky-thi'), $postData);

        $lichThiHocVien = LichThiHocVien::latest()->first();

        $this->assertSame($postData['hoc_vien_id'], $lichThiHocVien->hoc_vien_id);
        $this->assertSame($postData['lich_thi_id'], $lichThiHocVien->lich_thi_id);
        $this->assertSame(null, $lichThiHocVien->tinh_trang);
        $this->assertSame(null, $lichThiHocVien->ket_qua);

        /*----------  Test đăng ký thi ở lịch thi đã đăng ký  ----------*/
        $response = $this
            ->actingAs($this->user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.dang-ky-thi'), $postData);

        $response
            ->assertSessionHas('status', 'error')
            ->assertRedirect( route('hoc-vien.edit', $hocVien->id) );
    }

    public function test_hoc_vien_can_update_lich_thi(): void
    {
        $hocVien = HocVien::first();
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
            ->actingAs($this->user)
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
            ->actingAs($this->user)
            ->from( route('hoc-vien.edit', $hocVien->id) )
            ->post( route('hoc-vien.update-lich-thi'), $postData);

        $lichThiHocVien->refresh();

        $this->assertSame($postData['tinh_trang'], $lichThiHocVien->tinh_trang);
        $this->assertSame($postData['ket_qua'], $lichThiHocVien->ket_qua);
    }

    public function test_hoc_vien_can_xoa_lich_thi(): void
    {
        $hocVien = HocVien::first();
        $chungChi = ChungChi::factory()->create();
        $lichThi = LichThi::factory()->create();
        $lichThiHocVien = LichThiHocVien::factory()->create([
            'hoc_vien_id' => $hocVien->id,
            'lich_thi_id' => $lichThi->id,
            'tinh_trang' => 0,
            'ket_qua' => 0
        ]);

        $response = $this
            ->actingAs($this->user)
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
            'ngay_sinh' => '2000-01-01',
            'gioi_tinh' => 1,
        ];

        if ($id) {
            $data['id'] = $id;
        }

        return $data;
    }

    public static function searchData()
    {
        return [
            'filter by lop' => [ 'lop', '1', 1, 'Nguyen XYZ' ],
            'filter by ten' => [ 'ten', 'AB', 2, 'Le ABD' ],
            'filter by ho' => [ 'ten', 'Nguyen', 1, 'Nguyen XYZ' ],
            'filter by email' => [ 'email', 'hv3', 1, 'Le ABD' ],
            'filter by dien_thoai' => [ 'dien_thoai', '123', 1, 'Nguyen XYZ' ],
        ];
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

    protected function createHocPhiForHocVien($hocVien, $count = 1)
    {
        HocPhi::factory($count)->create([
            'hoc_vien_id' => $hocVien->id,
        ]);
    }

    protected function createLichThiForHocVien($hocVien, $count = 1)
    {
        ChungChi::factory($count)->create();
        LichThi::factory($count)->create()->each(function ($lich) use ($hocVien) {
            $lich->hoc_vien()->attach($hocVien->id);
        });
    }
}
