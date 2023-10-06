<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import { router, useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2';
import MyTable from '@/Components/MyTable.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps([
    'list',
    'filter',
    'sort',
    'listChungChi'
]);

const columns = [
    { title: 'ID', field: 'id' },
    { title: 'Chứng chỉ', field: 'chung_chi' },
    { title: 'Ngày thi', field: 'ngay_thi' },
    { title: 'Giờ thi', field: 'gio_thi' },
    { title: 'Địa điểm', field: 'dia_diem' },
];

const formFilter = useForm({
    filter: {
        chung_chi_id: props.filter?.chung_chi_id ?? 0,
        show_all: props.filter?.show_all ?? false,
    },
    sort: props.sort ?? ''
});

function search() {
    formFilter.get(route('lich-thi.index'));
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
            router.visit(route('lich-thi.delete'), {
                method: 'post',
                data: { id: item.id },
            });
        }
    })
}

</script>

<template>
    <Head title="Lịch thi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lịch thi</h2>
        </template>

        <Card>
            <div class="flex justify-between mb-3">
                <h3 class="text-lg font-semibold">Tìm lịch thi</h3>
            </div>

            <form @submit.prevent="search" class="md:grid grid-cols-3 gap-3 flex flex-col">
                <div class="flex space-x-2 items-center">
                    <InputLabel value="Chứng chỉ" class="shrink-0" />
                    <select class="form-select w-full grow-0" v-model="formFilter.filter.chung_chi_id">
                        <option :value="0">-- Chọn chứng chi --</option>
                        <option v-for="item in listChungChi" :value="item.id">{{ item.ten }}</option>
                    </select>
                </div>

                <div class="flex space-x-2 items-center">
                    <InputLabel value="Sắp xếp" class="shrink-0" />
                    <select class="form-select w-full grow-0" v-model="formFilter.sort">
                        <option value="">-- Chọn sắp xếp --</option>
                        <option value="ngay_thi">Ngày thi tăng dần</option>
                        <option value="-ngay_thi">Ngày thi giảm dần</option>
                        <option value="id">Id tăng dần</option>
                        <option value="-id">Id giảm dần</option>
                    </select>
                </div>

                <div class="flex space-x-2 items-center">
                    <InputLabel value="Hiện lịch thi đã qua" class="shrink-0" />
                    <input type="checkbox"
                        v-model="formFilter.filter.show_all"
                        true-value="1"
                        false-value="0"
                    />
                </div>

                <div class="col-span-3">
                    <PrimaryButton>Tìm kiếm</PrimaryButton>
                </div>
            </form>
        </Card>

        <Card>
            <div class="flex justify-between mb-3">
                <h3 class="text-xl font-semibold">Lịch thi</h3>
                <Link class="button-primary btn-add-new" :href="route('lich-thi.create')">Thêm mới</Link>
            </div>

            <MyTable
                :rows="list.data"
                :columns="columns"
                :links="list.links"
                :current-page="list.current_page"
            >
                <template #cot_chung_chi="{item}">
                    <Link :href="route('chung-chi.edit', item.chung_chi.id)">{{ item.chung_chi.ten }}</Link>
                </template>

                <template #actions="{ item }" >
                    <Link class="button-primary mr-2" :href="route('lich-thi.edit', item)">Sửa</Link>
                    <a class="button-danger" href="#" @click.prevent="deleteItem(item)">Xoá</a>
                </template>
            </MyTable>
        </Card>
    </AuthenticatedLayout>
</template>
