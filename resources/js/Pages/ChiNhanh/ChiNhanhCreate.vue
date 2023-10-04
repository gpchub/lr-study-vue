<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import MyTable from '@/Components/MyTable.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps([]);

const form = useForm({
    ten: '',
    email: '',
    dia_chi: '',
    dien_thoai: '',
});

</script>

<template>
    <Head title="Thêm chi nhánh" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thông tin chi nhánh</h2>
        </template>

        <div class="md:w-1/2">
            <Card>
                <form @submit.prevent="form.post(route('chi-nhanh.store'))" class="space-y-6">
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
        </div>

    </AuthenticatedLayout>
</template>
