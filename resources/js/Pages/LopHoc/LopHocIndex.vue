<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import { router, useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2';
import MyTable from '@/Components/MyTable.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps([
    'list',
    'filter',
    'sort',
    'listChiNhanh',
    'listGiaoVien',
]);

const columns = [
    { title: 'ID', field: 'id' },
    { title: 'Tên', field: 'ten' },
    { title: 'Ca học', field: 'ca_hoc' },
    { title: 'Chi nhánh', field: 'chi_nhanh' },
    { title: 'Giáo viên', field: 'giao_vien' },
];

const formFilter = useForm({
    filter: {
        chi_nhanh_id: props.filter?.chi_nhanh_id ?? null,
        giao_vien_id: props.filter?.giao_vien_id ?? null,
    },
    sort: props.sort ?? null
});

function search() {
    formFilter.get(route('lop-hoc.index'));
}

function deleteItem(item) {
    Swal.fire({
        title: 'Bạn có chắc muốn xoá không?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Đồng ý!',
        cancelButtonText: 'Huỷ',
    }).then((result) => {
        if (result.isConfirmed) {
            router.visit(route('lop-hoc.delete'), {
                method: 'post',
                data: { id: item.id },
            });
        }
    })
}

</script>

<template>
    <Head title="Lớp học" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lớp học</h2>
        </template>

        <Card>
            <div class="flex justify-between mb-3">
                <h3 class="text-lg font-semibold">Tìm lớp học</h3>
            </div>

            <form @submit.prevent="search" class="md:grid grid-cols-4 gap-3 flex flex-col">
                <div class="flex space-x-2 items-center">
                    <InputLabel value="Chi nhánh" />
                    <select class="form-select" v-model="formFilter.filter.chi_nhanh_id">
                        <option :value="null">-- Chọn chi nhánh --</option>
                        <option v-for="item in listChiNhanh" :value="item.id">{{ item.ten }}</option>
                    </select>
                </div>
                <div class="flex space-x-2 items-center">
                    <InputLabel value="Giáo viên" />
                    <select class="form-select" v-model="formFilter.filter.giao_vien_id">
                        <option :value="null">-- Chọn giáo viên --</option>
                        <option v-for="item in listGiaoVien" :value="item.id">{{ item.ho_va_ten }}</option>
                    </select>
                </div>
                <div class="flex space-x-2 items-center">
                    <InputLabel value="Sắp xếp" />
                    <select class="form-select" v-model="formFilter.sort">
                        <option value="">-- Chọn sắp xếp --</option>
                        <option value="ten">Tên A-Z</option>
                        <option value="-ten">Tên Z-A</option>
                        <option value="id">Id tăng dần</option>
                        <option value="-id">Id giảm dần</option>
                    </select>
                </div>
                <div class="col-span-4">
                    <PrimaryButton>Tìm kiếm</PrimaryButton>
                </div>
            </form>
        </Card>

        <Card>
            <div class="flex justify-between mb-3">
                <h3 class="text-xl font-semibold">Danh sách lớp học</h3>
                <Link class="button-primary btn-add-new" :href="route('lop-hoc.create')">Thêm mới</Link>
            </div>

            <MyTable
                :rows="list.data"
                :columns="columns"
                :links="list.links"
                :current-page="list.current_page"
            >
                <template #cot_ten="{ item }">
                    <Link :href="route('lop-hoc.edit', item)">{{item.ten}}</Link>
                </template>

                <template #cot_chi_nhanh="{ item }">
                    <Link :href="route('chi-nhanh.edit', item)">{{item.chi_nhanh.ten}}</Link>
                </template>

                <template #cot_giao_vien="{ item }">
                    <Link :href="route('giao-vien.edit', item)">{{ item.giao_vien.ho_va_ten }}</Link>
                </template>

                <template #actions="{ item }" >
                    <Link class="button-primary mr-2" :href="route('lop-hoc.edit', item)">Sửa</Link>
                    <a class="button-danger" href="#" @click.prevent="deleteItem(item)">Xoá</a>
                </template>
            </MyTable>
        </Card>
    </AuthenticatedLayout>
</template>
