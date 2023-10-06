<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { router } from '@inertiajs/vue3'
import Swal from 'sweetalert2';
import Modal from '@/Components/Modal.vue';
import { ref, computed, onMounted } from 'vue';
import MyDatepicker from '@/Components/MyDatepicker.vue';
import MyTable from '@/Components/MyTable.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps([]);

const form = useForm({
    ho: '',
    ten: '',
    email: '',
    ngay_sinh: '',
    dien_thoai: '',
    gioi_tinh: 0,
});

function submitForm() {
    form.post(route('giao-vien.store'));
}

</script>

<template>
    <Head title="Thêm giáo viên" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thông tin giáo viên</h2>
        </template>

        <div class="md:grid grid-cols-2 gap-3">
            <Card>
                <div class="flex justify-between mb-3">
                    <h3 class="text-xl font-semibold">Thông tin giáo viên</h3>
                </div>
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div>
                        <InputLabel for="ho" value="Họ và chữ lót" />
                        <TextInput id="ho" type="text" class="mt-1 block w-full" v-model="form.ho"  autofocus />
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
        </div>

    </AuthenticatedLayout>
</template>
