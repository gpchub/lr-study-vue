<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import MyTable from '@/Components/MyTable.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps([
    'listHocVien',
    'listChiNhanh',
    'listGiaoVien',
    'lopHoc'
]);

const columns = [
    { title: 'ID', field: 'id' },
    { title: 'Tên', field: 'ho_va_ten' },
];

const form = useForm({
    id: props.lopHoc.id,
    ten: props.lopHoc.ten,
    ca_hoc: props.lopHoc.ca_hoc,
    chi_nhanh_id: props.lopHoc.chi_nhanh_id,
    giao_vien_id: props.lopHoc.giao_vien_id,
});

const caHoc = [
    '246 - 05:45',
    '246 - 08:00',
    '246 - 17:30',
    '246 - 18:15',
    '246 - 18:45',
    '246 - 20:00',
    '357 - 06:00',
    '357 - 08:00',
    '357 - 17:00',
    '357 - 18:15',
    '357 - 18:30',
    '357 - 19:30',
];

</script>

<template>
    <Head title="Chỉnh sửa lớp học" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thông tin lớp học</h2>
        </template>

        <div class="grid grid-cols-2 gap-3">
            <Card>
                <div class="flex justify-between mb-3">
                    <h3 class="text-xl font-semibold">Thông tin lớp học</h3>
                </div>
                <form @submit.prevent="form.patch(route('lop-hoc.update'))" class="space-y-6">
                    <div>
                        <InputLabel for="ten" value="Tên" />
                        <TextInput id="ten" type="text" class="mt-1 block w-full" v-model="form.ten" required autofocus />
                        <InputError class="mt-2" :message="form.errors.ten" />
                    </div>

                    <div>
                        <InputLabel for="chi_nhanh_id" value="Chi nhánh" />
                        <select id="chi_nhanh_id" class="mt-1 block w-full form-select" v-model="form.chi_nhanh_id" required>
                            <option :value="null">--- Chọn chi nhánh ---</option>
                            <option v-for="item in listChiNhanh" :value="item.id">{{ item.ten }}</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.chi_nhanh_id" />
                    </div>

                    <div>
                        <InputLabel for="giao_vien_id" value="Giáo viên" />
                        <select id="giao_vien_id" class="mt-1 block w-full form-select" v-model="form.giao_vien_id" required>
                            <option :value="null">--- Chọn giáo viên ---</option>
                            <option v-for="item in listGiaoVien" :value="item.id">{{ item.ho_va_ten }}</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.giao_vien_id" />
                    </div>

                    <div>
                        <InputLabel for="ca_hoc" value="Ca học" />
                        <select id="ca_hoc" class="mt-1 block w-full form-select" v-model="form.ca_hoc" required>
                            <option :value="null">--- Chọn ca học ---</option>
                            <option v-for="item in caHoc" :value="item">{{ item }}</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.ca_hoc" />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">Lưu lại</PrimaryButton>
                    </div>
                </form>
            </Card>

            <Card>
                <div class="flex justify-between mb-3">
                    <h3 class="text-xl font-semibold">Danh sách học viên</h3>
                </div>

                <MyTable
                    :rows="listHocVien.data"
                    :columns="columns"
                    :links="listHocVien.links"
                    :current-page="listHocVien.current_page"
                    :has-actions="false"
                >
                    <template #cot_ho_va_ten="{ item }">
                        <Link :href="route('hoc-vien.edit', item.id)">{{ item.ho_va_ten }}</Link>
                    </template>
                </MyTable>
            </Card>
        </div>

    </AuthenticatedLayout>
</template>
