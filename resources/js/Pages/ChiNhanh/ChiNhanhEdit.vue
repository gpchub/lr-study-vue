<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import MyTable from '@/Components/MyTable.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps([
    'danhSachLop',
    'chiNhanh'
]);

const columns = [
    { title: 'ID', field: 'id' },
    { title: 'Tên', field: 'ten' },
];

const form = useForm({
    id: props.chiNhanh.id,
    ten: props.chiNhanh.ten,
    email: props.chiNhanh.email,
    dia_chi: props.chiNhanh.dia_chi,
    dien_thoai: props.chiNhanh.dien_thoai,
});

</script>

<template>
    <Head title="Chỉnh sửa chi nhánh" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thông tin chi nhánh</h2>
        </template>

        <div class="grid grid-cols-2 gap-3">
            <Card>
                <form @submit.prevent="form.patch(route('chi-nhanh.update'))" class="space-y-6">
                    <div>
                        <InputLabel for="ten" value="Tên" />
                        <TextInput id="ten" type="text" class="mt-1 block w-full" v-model="form.ten" required autofocus />
                        <InputError class="mt-2" :message="form.errors.ten" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="dia_chi" value="Địa chỉ" />
                        <TextInput id="dia_chi" type="text" class="mt-1 block w-full" v-model="form.dia_chi" required />
                        <InputError class="mt-2" :message="form.errors.dia_chi" />
                    </div>

                    <div>
                        <InputLabel for="dien_thoai" value="Điện thoại" />
                        <TextInput id="dien_thoai" type="text" class="mt-1 block w-full" v-model="form.dien_thoai" required  />
                        <InputError class="mt-2" :message="form.errors.dien_thoai" />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">Lưu lại</PrimaryButton>
                    </div>
                </form>
            </Card>

            <Card>
                <div class="flex justify-between mb-3">
                    <h3 class="text-xl font-semibold">Danh sách lớp</h3>
                </div>

                <MyTable
                    :rows="danhSachLop"
                    :columns="columns"
                >
                    <template #cot_ten="{ item }">
                        <Link :href="route('lop-hoc.edit', item)">{{item.ten}}</Link>
                    </template>
                </MyTable>
            </Card>
        </div>

    </AuthenticatedLayout>
</template>
