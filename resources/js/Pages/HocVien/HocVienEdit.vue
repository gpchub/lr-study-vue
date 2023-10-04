<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import MyTable from '@/Components/MyTable.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import MyDatepicker from '@/Components/MyDatepicker.vue';
import Modal from '@/Components/Modal.vue';
import { computed } from 'vue';
import Swal from 'sweetalert2'

const props = defineProps([
    'listLopHocVien',
    'listLopHocVienIds',
    'listLopHoc',
    'hocVien',
]);

const listLopChuaHoc = computed(() => {
    return props.listLopHoc.filter(x => !props.listLopHocVienIds.includes(x.id));
});

const listLopDangHoc = computed(() => {
    return props.listLopHoc.filter(x => props.listLopHocVienIds.includes(x.id));
});

const columns = [
    { title: 'ID', field: 'id' },
    { title: 'Tên', field: 'ten' },
    { title: 'Ngày bắt đầu', field: 'ngay_bat_dau' },
];

const form = useForm({
    id: props.hocVien.id,
    ho: props.hocVien.ho,
    ten: props.hocVien.ten,
    email: props.hocVien.email,
    ngay_sinh: props.hocVien.ngay_sinh,
    dien_thoai: props.hocVien.dien_thoai,
    gioi_tinh: props.hocVien.gioi_tinh,
});

const formDangKyLop = useForm({
    hoc_vien_id: props.hocVien.id,
    lop_hoc_id: null,
    ngay_bat_dau: '',
});

const today = new Date();
const formDongHocPhi = useForm({
    hoc_vien_id: props.hocVien.id,
    lop_hoc_id: null,
    ngay_dong: `${today.getFullYear()}/${today.getMonth() + 1}/${today.getDate()}`,
    thang: today.getMonth() + 1,
    nam: today.getFullYear(),
    so_tien: '',
});

const openPopupDangKyLop = ref(false);
const openPopupDongHocPhi = ref(false);
const openPopupXemHocPhi = ref(false);

const listHocPhiDaDong = ref(false);
let xemHocPhiLop = '';
const hocPhiTableColumns = [
    { title: 'ID', field: 'id' },
    { title: 'Khoá', field: 'khoa' },
    { title: 'Số tiền', field: 'so_tien' },
    { title: 'Ngày đóng', field: 'ngay_dong' },
];

function submitForm() {
    form.patch(route('hoc-vien.update'));
}

function dangKyLop() {
    openPopupDangKyLop.value = true;
}

function submitFormDangKyLop() {
    formDangKyLop.post(route('hoc-vien.dang-ky-lop'), {
        onSuccess: () => {
            formDangKyLop.reset();
            openPopupDangKyLop.value = false;
        }
    });
}

function dongHocPhi() {
    openPopupDongHocPhi.value = true;
}

function submitFormDongHocPhi() {
    formDongHocPhi.post(route('hoc-vien.dong-hoc-phi'), {
        onSuccess: () => {
            formDongHocPhi.reset();
            openPopupDongHocPhi.value = false;
        }
    });
}

function xemHocPhi(item) {
    xemHocPhiLop = item.ten;
    axios.get(route('hoc-vien.xem-hoc-phi'), {
        params: {
            lop_hoc_id: item.lop_hoc_id,
            hoc_vien_id: item.hoc_vien_id
        }
    }).then((response) => {
        let data = response.data;
        listHocPhiDaDong.value = data;
        openPopupXemHocPhi.value = true;
    });
}


function xoaLop(item) {
    Swal.fire({
        title: 'Bạn có chắc muốn xoá?',
        showDenyButton: true,
        confirmButtonText: 'Đồng ý',
        denyButtonText: `Huỷ`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            router.post(route('hoc-vien.xoa-lop'), {
                id: item.lop_hoc_vien_id,
            });
        }
    })
}
// tạo mảng dãy số liên tục
// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/from#sequence_generator_range
const range = (start, stop, step) =>
  Array.from({ length: (stop - start) / step + 1 }, (_, i) => start + i * step);

const listYears = range(1950, new Date().getFullYear(), 1);

</script>

<template>
    <Head title="Chỉnh sửa học viên" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thông tin học viên</h2>
        </template>

        <div class="md:grid grid-cols-3 gap-3">
            <Card>
                <div class="flex justify-between mb-3">
                    <h3 class="text-xl font-semibold">Thông tin học viên</h3>
                </div>
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div>
                        <InputLabel for="ho" value="Họ và chữ lót" />
                        <TextInput id="ho" type="text" class="mt-1 block w-full" v-model="form.ho" required autofocus />
                        <InputError class="mt-2" :message="form.errors.ho" />
                    </div>

                    <div>
                        <InputLabel for="ten" value="Tên" />
                        <TextInput id="ten" type="text" class="mt-1 block w-full" v-model="form.ten" required  />
                        <InputError class="mt-2" :message="form.errors.ten" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="dien_thoai" value="Điện thoại" />
                        <TextInput id="dien_thoai" type="text" class="mt-1 block w-full" v-model="form.dien_thoai" required  />
                        <InputError class="mt-2" :message="form.errors.dien_thoai" />
                    </div>

                    <div>
                        <InputLabel for="gioi_tinh" value="Giới tính" />
                        <select id="gioi_tinh" class="mt-1 block w-full form-select" v-model="form.gioi_tinh">
                            <option :value="0">--- Chọn giới tính ---</option>
                            <option value="1">Nam</option>
                            <option value="2">Nữ</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.gioi_tinh" />
                    </div>

                    <div>
                        <InputLabel for="ngay_sinh" value="Ngày sinh" />
                        <MyDatepicker id="ngay_sinh" class="mt-1 block w-full" v-model="form.ngay_sinh" required  />
                        <InputError class="mt-2" :message="form.errors.ngay_sinh" />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">Lưu lại</PrimaryButton>
                    </div>
                </form>
            </Card>

            <Card class="col-span-2">
                <div class="flex justify-between mb-3">
                    <h3 class="text-xl font-semibold">Lớp học</h3>
                    <div class="space-x-1">
                        <PrimaryButton type="button" @click="dangKyLop">Đăng ký lớp</PrimaryButton>
                        <PrimaryButton type="button" @click="dongHocPhi">Đóng học phí</PrimaryButton>
                    </div>

                </div>

                <MyTable
                    :rows="listLopHocVien"
                    :columns="columns"
                >
                    <template #cot_id="{ item }">{{ item.lop_hoc_vien_id }}</template>
                    <template #cot_ten="{ item }">
                        <Link :href="route('lop-hoc.edit', item.id)">{{ item.ten }}</Link>
                    </template>
                    <template #actions="{item}">
                        <a href="#" @click.prevent="xemHocPhi(item)">Xem học phí</a>
                        <a href="#" @click.prevent="xoaLop(item)" class="ml-3 text-red-500">Xoá</a>
                    </template>
                </MyTable>
            </Card>
        </div>

        <Modal :show="openPopupDangKyLop" @close="openPopupDangKyLop = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-3">
                    Đăng ký lớp
                </h2>

                <form @submit.prevent="submitFormDangKyLop" class="space-y-6">
                    <div>
                        <InputLabel for="lop_hoc_id" value="Lớp học" />
                        <select class="mt-1 block w-full form-select" v-model="formDangKyLop.lop_hoc_id">
                            <option :value="null">--- Chọn lớp học ---</option>
                            <option v-for="item in listLopChuaHoc" :value="item.id">{{ item.ten }}</option>
                        </select>
                        <InputError class="mt-2" :message="formDangKyLop.errors.lop_hoc_id" />
                    </div>

                    <div>
                        <InputLabel for="ngay_bat_dau" value="Ngày bắt đầu" />
                        <MyDatepicker id="ngay_bat_dau" class="mt-1 block w-full" v-model="formDangKyLop.ngay_bat_dau" required  />
                        <InputError class="mt-2" :message="formDangKyLop.errors.ngay_bat_dau" />
                    </div>

                    <div class="flex space-x-1">
                        <PrimaryButton :disabled="form.processing">Lưu lại</PrimaryButton>
                        <SecondaryButton @click="openPopupDangKyLop = false"> Huỷ </SecondaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="openPopupDongHocPhi" @close="openPopupDongHocPhi = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-3">
                    Đóng học phí
                </h2>

                <form @submit.prevent="submitFormDongHocPhi" class="space-y-6">
                    <div>
                        <InputLabel for="lop_hoc_id" value="Lớp đang học" />
                        <select class="mt-1 block w-full form-select" v-model="formDongHocPhi.lop_hoc_id">
                            <option :value="null">--- Chọn lớp học ---</option>
                            <option v-for="item in listLopDangHoc" :value="item.id">{{ item.ten }}</option>
                        </select>
                        <InputError class="mt-2" :message="formDongHocPhi.errors.lop_hoc_id" />
                    </div>

                    <div class="md:grid grid-cols-2 gap-2">
                        <div>
                            <InputLabel for="thang" value="Tháng" />
                            <select id="thang" class="mt-1 block w-full form-select" v-model="formDongHocPhi.thang">
                                <option v-for="item in 12" :value="item">{{ item }}</option>
                            </select>
                            <InputError class="mt-2" :message="formDongHocPhi.errors.thang" />
                        </div>
                        <div>
                            <InputLabel for="nam" value="Năm" />
                            <select id="nam" class="mt-1 block w-full form-select" v-model="formDongHocPhi.nam">
                                <option v-for="item in listYears" :value="item">{{ item }}</option>
                            </select>
                            <InputError class="mt-2" :message="formDongHocPhi.errors.nam" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="so_tien" value="Số tiền" />
                        <TextInput id="so_tien" type="number" class="mt-1 block w-full" v-model="formDongHocPhi.so_tien" required  />
                        <InputError class="mt-2" :message="formDongHocPhi.errors.so_tien" />
                    </div>

                    <div>
                        <InputLabel for="ngay_dong" value="Ngày đóng" />
                        <MyDatepicker id="ngay_dong" class="mt-1 block w-full" v-model="formDongHocPhi.ngay_dong" required  />
                        <InputError class="mt-2" :message="formDongHocPhi.errors.ngay_dong" />
                    </div>

                    <div class="flex space-x-1">
                        <PrimaryButton :disabled="formDongHocPhi.processing">Lưu lại</PrimaryButton>
                        <SecondaryButton @click="openPopupDongHocPhi = false"> Huỷ </SecondaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="openPopupXemHocPhi" @close="openPopupXemHocPhi = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-3">
                    Lịch sử đóng học phí ({{ xemHocPhiLop }})
                </h2>
                <MyTable
                    :rows="listHocPhiDaDong"
                    :columns="hocPhiTableColumns"
                    :has-actions="false"
                >
                <template #cot_khoa="{ item }">
                    {{item.thang}}/{{ item.nam }}
                </template>
            </MyTable>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
