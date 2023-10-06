<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {  ref } from 'vue';
import Swal from 'sweetalert2'
import MyTable from '@/Components/MyTable.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { onMounted } from 'vue';

const props = defineProps(['list']);

const columns = [
    { title: 'ID', field: 'id' },
    { title: 'Họ tên', field: 'ho_va_ten' },
    { title: 'Giới tính', field: 'gioi_tinh' },
    { title: 'Email', field: 'email' },
    { title: 'Điện thoại', field: 'dien_thoai' },
    { title: 'Ngày sinh', field: 'ngay_sinh' },
];

onMounted(() => {
    window.print();
});

</script>

<template>
    <Head title="Học viên" />
    <MyTable :rows="list" :columns="columns" :has-actions="false" />
</template>
<style>
@media print {


    table { page-break-inside:auto }
    div   { page-break-inside:avoid; } /* This is the key */
    thead { display:table-header-group }
    tfoot { display:table-footer-group }
}
</style>