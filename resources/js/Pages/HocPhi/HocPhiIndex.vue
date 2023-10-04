<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {  ref } from 'vue';
import Swal from 'sweetalert2'
import MyTable from '@/Components/MyTable.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps(['list', 'listHocVien', 'listLopHoc', 'filter', 'sort']);

const columns = [
    { title: 'ID', field: 'id' },
    { title: 'Học viên', field: 'hoc_vien' },
    { title: 'Lớp', field: 'lop_hoc' },
    { title: 'Khoá', field: 'khoa' },
    { title: 'Số tiền', field: 'so_tien' },
    { title: 'Ngày đóng', field: 'ngay_dong' },
];

const formFilter = useForm({
    filter: {
        hoc_vien_id: props.filter?.hoc_vien_id ?? 0,
        lop_hoc_id: props.filter?.lop_hoc_id ?? 0,
        khoa: props.filter?.khoa ?? '',
    },
    sort: props.sort ?? null
});

function search() {
    formFilter.get(route('hoc-phi.index'));
}

function deleteItem(item) {
    Swal.fire({
        title: 'Bạn có chắc muốn xoá?',
        showDenyButton: true,
        confirmButtonText: 'Đồng ý',
        denyButtonText: `Huỷ`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            router.post(route('hoc-phi.delete'), {
                id: item.id,
            });
        }
    })
}

</script>

<template>
    <Head title="Học phí" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Học Phí</h2>
        </template>
        <Card>
            <div class="flex justify-between mb-3">
                <h3 class="text-lg font-semibold">Bộ lọc</h3>
            </div>

            <form @submit.prevent="search" class="md:grid grid-cols-3 gap-3 flex flex-col">
                <div class="flex space-x-2 items-center">
                    <InputLabel value="Học lớp" class="shrink-0" />
                    <select class="form-select w-full grow-0" v-model="formFilter.filter.lop_hoc_id">
                        <option :value="0">-- Chọn lớp học --</option>
                        <option v-for="item in listLopHoc" :value="item.id">{{ item.ten }}</option>
                    </select>
                </div>
                <div class="flex space-x-2 items-center">
                    <InputLabel value="Học viên" class="shrink-0" />
                    <select class="form-select w-full grow-0" v-model="formFilter.filter.hoc_vien_id">
                        <option :value="0">-- Chọn học viên --</option>
                        <option v-for="item in listHocVien" :value="item.id">{{ item.ho }} {{ item.ten }}</option>
                    </select>
                </div>
                <div class="flex space-x-2 items-center">
                    <InputLabel value="Sắp xếp" class="shrink-0" />
                    <select class="form-select w-full grow-0" v-model="formFilter.sort">
                        <option :value="''">-- Chọn sắp xếp --</option>
                        <option value="ten_hoc_vien">Tên học viên A-Z</option>
                        <option value="-ten_hoc_vien">Tên học viên Z-A</option>
                        <option value="ten_lop">Tên lớp A-Z</option>
                        <option value="-ten_lop">Tên lớp Z-A</option>
                        <option value="ngay_dong">Ngày đóng tăng dần</option>
                        <option value="-ngay_dong">Ngày đóng giảm dần</option>
                    </select>
                </div>
                <div class="col-span-3">
                    <PrimaryButton>Tìm kiếm</PrimaryButton>
                </div>
            </form>
        </Card>

        <Card>
            <div class="flex justify-between mb-3">
                <h3 class="text-xl font-semibold">Danh sách học phí</h3>
            </div>

            <MyTable
                :rows="list.data"
                :columns="columns"
                :links="list.links"
                :current-page="list.current_page"
            >
                <template #cot_hoc_vien="{ item }">
                    <Link :href="route('hoc-vien.edit', item.hoc_vien.id)">{{item.hoc_vien.ho_va_ten}}</Link>
                </template>

                <template #cot_lop_hoc="{ item }">
                    <Link :href="route('lop-hoc.edit', item.lop_hoc.id)">{{item.lop_hoc.ten}}</Link>
                </template>

                <template #cot_khoa="{ item }">
                    {{item.thang}}/{{ item.nam }}
                </template>

                <template #actions="{ item }" >
                    <Link class="button-primary mr-2" :href="route('hoc-phi.edit', item)">Sửa</Link>
                    <a class="button-danger" href="#" @click.prevent="deleteItem(item)">Xoá</a>
                </template>
            </MyTable>

        </Card>
    </AuthenticatedLayout>
</template>
