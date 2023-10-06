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

const props = defineProps(['listChungChi']);

const form = useForm({
    chung_chi_id: 0,
    ngay_thi: '',
    dia_diem: '',
});

</script>

<template>
    <Head title="Thêm lịch thi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thông tin lịch thi</h2>
        </template>

        <div class="md:w-1/2">
            <Card>
                <form @submit.prevent="form.post(route('lich-thi.store'))" class="space-y-6">
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
        </div>

    </AuthenticatedLayout>
</template>
