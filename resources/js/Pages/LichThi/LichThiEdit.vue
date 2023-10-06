<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import MyTable from '@/Components/MyTable.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import MyDatepicker from '@/Components/MyDatepicker.vue'

const props = defineProps([
    'lichThi',
    'listHocVien',
    'listChungChi'
]);

const columns = [
    { title: 'ID', field: 'id' },
    { title: 'Tên', field: 'ho_va_ten' },
];

const form = useForm({
    id: props.lichThi.id,
    chung_chi_id: props.lichThi.chung_chi_id,
    ngay_thi: props.lichThi.ngay_thi,
    dia_diem: props.lichThi.dia_diem,
});

</script>

<template>
    <Head title="Chỉnh sửa lịch thi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thông tin lịch thi</h2>
        </template>

        <div class="grid grid-cols-2 gap-3">
            <Card>
                <form @submit.prevent="form.patch(route('lich-thi.update'))" class="space-y-6">
                    <div class="items-center">
                        <InputLabel value="Chứng chỉ" class="shrink-0" />
                        <select class="form-select w-full grow-0" v-model="form.chung_chi_id">
                            <option :value="0">-- Chọn chứng chi --</option>
                            <option v-for="item in listChungChi" :value="item.id">{{ item.ten }}</option>
                        </select>
                    </div>

                    <div>
                        <InputLabel for="ngay_thi" value="Ngày thi" />
                        <MyDatepicker class="mt-1 block w-full" v-model="form.ngay_thi" has-time-picker min-date="today"></MyDatepicker>
                        <InputError class="mt-2" :message="form.errors.ngay_thi" />
                    </div>

                    <div>
                        <InputLabel for="dia_diem" value="Địa điểm" />
                        <TextInput id="dia_diem" type="text" class="mt-1 block w-full" v-model="form.dia_diem" required />
                        <InputError class="mt-2" :message="form.errors.dia_diem" />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">Lưu lại</PrimaryButton>
                    </div>
                </form>
            </Card>

            <Card>
                <div class="flex justify-between mb-3">
                    <h3 class="text-xl font-semibold">Học viên</h3>
                </div>

                <MyTable
                    :rows="listHocVien.data"
                    :columns="columns"
                    :links="listHocVien.links"
                    :current-page="listHocVien.current_page"

                >
                    <template #cot_ho_va_ten="{ item }">
                        <Link :href="route('hoc-vien.edit', item.id)">{{ item.ho_va_ten }}</Link>
                    </template>
                </MyTable>
            </Card>
        </div>

    </AuthenticatedLayout>
</template>
