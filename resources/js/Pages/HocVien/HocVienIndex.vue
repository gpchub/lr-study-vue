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

const props = defineProps(['list', 'filter', 'sort', 'listLopHoc']);

const formFilter = useForm({
    filter: {
        ten: props.filter?.ten ?? '',
        email: props.filter?.email ?? '',
        dien_thoai: props.filter?.dien_thoai ?? '',
        lop: props.filter?.lop ?? 0,
    },
    sort: props.sort ?? null
});

const columns = [
    { title: 'ID', field: 'id' },
    { title: 'Họ tên', field: 'ho_va_ten' },
    { title: 'Giới tính', field: 'gioi_tinh' },
    { title: 'Email', field: 'email' },
    { title: 'Điện thoại', field: 'dien_thoai' },
    { title: 'Ngày sinh', field: 'ngay_sinh' },
];

function search() {
    formFilter.get(route('hoc-vien.index'));
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
            router.post(route('hoc-vien.delete'), {
                id: item.id,
            });
        }
    })
}

</script>

<template>
    <Head title="Học viên" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Học Viên</h2>
        </template>

        <Card>
            <div class="flex justify-between mb-3">
                <h3 class="text-lg font-semibold">Tìm học viên</h3>
            </div>

            <form @submit.prevent="search" class="md:grid grid-cols-3 gap-3 flex flex-col">
                <div class="flex space-x-2 items-center">
                    <InputLabel value="Tên" class="shrink-0" />
                    <TextInput class="w-full" v-model="formFilter.filter.ten" />
                </div>
                <div class="flex space-x-2 items-center">
                    <InputLabel value="Email" class="shrink-0" />
                    <TextInput class="w-full" v-model="formFilter.filter.email" />
                </div>
                <div class="flex space-x-2 items-center">
                    <InputLabel value="Điện thoại" class="shrink-0" />
                    <TextInput class="w-full" v-model="formFilter.filter.dien_thoai" />
                </div>
                <div class="flex space-x-2 items-center">
                    <InputLabel value="Học lớp" class="shrink-0" />
                    <select class="form-select w-full grow-0" v-model="formFilter.filter.lop">
                        <option :value="0">-- Chọn lớp học --</option>
                        <option v-for="item in listLopHoc" :value="item.id">{{ item.ten }}</option>
                    </select>
                </div>
                <div class="flex space-x-2 items-center">
                    <InputLabel value="Sắp xếp" class="shrink-0" />
                    <select class="form-select w-full grow-0" v-model="formFilter.sort">
                        <option value="">-- Chọn sắp xếp --</option>
                        <option value="ten">Tên A-Z</option>
                        <option value="-ten">Tên Z-A</option>
                        <option value="id">Id tăng dần</option>
                        <option value="-id">Id giảm dần</option>
                    </select>
                </div>
                <div class="col-span-3">
                    <PrimaryButton>Tìm kiếm</PrimaryButton>
                </div>
            </form>
        </Card>

        <Card>
            <div class="flex justify-between mb-3">
                <h3 class="text-xl font-semibold">Danh sách học viên</h3>
                <Link class="button-primary btn-add-new" :href="route('hoc-vien.create')">Thêm mới</Link>
            </div>

            <MyTable
                :rows="list.data"
                :columns="columns"
                :links="list.links"
                :current-page="list.current_page"
            >
                <template #cot_ho_va_ten="{ item }">
                    <Link :href="route('hoc-vien.edit', item)">{{item.ho_va_ten}}</Link>
                </template>

                <template #actions="{ item }" >
                    <Link class="button-primary mr-2" :href="route('hoc-vien.edit', item)">Sửa</Link>
                    <a class="button-danger" href="#" @click.prevent="deleteItem(item)">Xoá</a>
                </template>
            </MyTable>

        </Card>
    </AuthenticatedLayout>
</template>
