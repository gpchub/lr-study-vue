<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import { router } from '@inertiajs/vue3'
import Swal from 'sweetalert2';
import MyTable from '@/Components/MyTable.vue';

const props = defineProps([
    'list',
]);

const columns = [
    { title: 'ID', field: 'id' },
    { title: 'Tên', field: 'ten' },
    { title: 'Email', field: 'email' },
    { title: 'Điện thoại', field: 'dien_thoai' },
    { title: 'Địa chỉ', field: 'dia_chi' },
];

function deleteItem(item) {
    Swal.fire({
        title: 'Bạn có chắc muốn xoá không?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Đồng ý!',
        cancelButtonText: 'Huỷ',
    }).then((result) => {
        if (result.isConfirmed) {
            router.visit(route('chi-nhanh.delete'), {
                method: 'post',
                data: { id: item.id },
            });
        }
    })
}

</script>

<template>
    <Head title="Chi nhánh" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Chi nhánh</h2>
        </template>

        <Card>
            <div class="flex justify-between mb-3">
                <h3 class="text-xl font-semibold">Danh sách chi nhánh</h3>
                <Link class="button-primary btn-add-new" :href="route('chi-nhanh.create')">Thêm mới</Link>
            </div>

            <MyTable
                :rows="list.data"
                :columns="columns"
                :links="list.links"
                :current-page="list.current_page"
            >
                <template #cot_ten="{ item }">
                    <Link :href="route('chi-nhanh.edit', item)">{{item.ten}}</Link>
                </template>

                <template #actions="{ item }" >
                    <Link class="button-primary mr-2" :href="route('chi-nhanh.edit', item)">Sửa</Link>
                    <a class="button-danger" href="#" @click.prevent="deleteItem(item)">Xoá</a>
                </template>
            </MyTable>
        </Card>
    </AuthenticatedLayout>
</template>
