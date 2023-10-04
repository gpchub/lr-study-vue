<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import MyTable from '@/Components/MyTable.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import MyDatepicker from '@/Components/MyDatepicker.vue';

const props = defineProps([
    'hocPhi'
]);

const form = useForm({
    id: props.hocPhi.id,
    so_tien: props.hocPhi.so_tien,
    thang: props.hocPhi.thang,
    nam: props.hocPhi.nam,
    ngay_dong: props.hocPhi.ngay_dong,
    hoc_vien_id: props.hocPhi.hoc_vien_id,
    lop_hoc_id: props.hocPhi.lop_hoc_id,
    hoc_vien: props.hocPhi.hoc_vien.ho_va_ten,
    lop_hoc: props.hocPhi.lop_hoc.ten,
});

function submitForm() {
    form.patch(route('hoc-phi.update'));
}

// tạo mảng dãy số liên tục
// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/from#sequence_generator_range
const range = (start, stop, step) =>
  Array.from({ length: (stop - start) / step + 1 }, (_, i) => start + i * step);

const listYears = range(1950, new Date().getFullYear(), 1);
</script>

<template>
    <Head title="Chỉnh sửa hoá đơn" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thông tin hoá đơn</h2>
        </template>

        <div class="md:w-1/2">
            <Card>
                <div class="flex justify-between mb-3">
                    <h3 class="text-xl font-semibold">Thông tin hoá đơn</h3>
                </div>
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div>
                        <InputLabel for="hoc_vien_id" value="Học viên" />
                        <TextInput id="hoc_vien_id" class="mt-1 block w-full" v-model="form.hoc_vien" disabled  />
                    </div>

                    <div>
                        <InputLabel for="lop_hoc_id" value="Lớp học" />
                        <TextInput id="lop_hoc_id" class="mt-1 block w-full" v-model="form.lop_hoc" disabled  />
                    </div>

                    <div class="md:grid grid-cols-2 gap-2">
                        <div>
                            <InputLabel for="thang" value="Tháng" />
                            <select id="thang" class="mt-1 block w-full form-select" v-model="form.thang" disabled>
                                <option v-for="item in 12" :value="item">{{ item }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.thang" />
                        </div>
                        <div>
                            <InputLabel for="nam" value="Năm" />
                            <select id="nam" class="mt-1 block w-full form-select" v-model="form.nam" disabled>
                                <option v-for="item in listYears" :value="item">{{ item }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.nam" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="so_tien" value="Số tiền" />
                        <TextInput id="so_tien" type="number" class="mt-1 block w-full" v-model="form.so_tien" required  />
                        <InputError class="mt-2" :message="form.errors.so_tien" />
                    </div>

                    <div>
                        <InputLabel for="ngay_dong" value="Ngày đóng" />
                        <MyDatepicker id="ngay_dong" class="mt-1 block w-full" v-model="form.ngay_dong" required  />
                        <InputError class="mt-2" :message="form.errors.ngay_dong" />
                    </div>


                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">Lưu lại</PrimaryButton>
                    </div>
                </form>
            </Card>
        </div>

    </AuthenticatedLayout>
</template>
