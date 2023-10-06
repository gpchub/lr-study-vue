<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import MyTable from '@/Components/MyTable.vue';
import Modal from '@/Components/Modal.vue';
import Swal from 'sweetalert2';

import DangKyLop from './DangKyLop.vue';
import DongHocPhi from './DongHocPhi.vue';

const props = defineProps([
    'listLopHocVien',
    'listLopHocVienIds',
    'listLopHoc',
    'hocVien',
]);

const listLopChuaHoc = computed(() => {
    return props.listLopHoc.filter(x => !props.listLopHocVienIds.includes(x.id));
});

const listLopDangHoc = computed(() => {
    return props.listLopHoc.filter(x => props.listLopHocVienIds.includes(x.id));
});

const columns = [
    { title: 'ID', field: 'id' },
    { title: 'Tên', field: 'ten' },
    { title: 'Ngày bắt đầu', field: 'ngay_bat_dau' },
];

const openPopupXemHocPhi = ref(false);

const listHocPhiDaDong = ref(false);
let xemHocPhiLop = '';
const hocPhiTableColumns = [
    { title: 'ID', field: 'id' },
    { title: 'Khoá', field: 'khoa' },
    { title: 'Số tiền', field: 'so_tien' },
    { title: 'Ngày đóng', field: 'ngay_dong' },
];

function xemHocPhi(item) {
    xemHocPhiLop = item.ten;
    axios.get(route('hoc-vien.xem-hoc-phi'), {
        params: {
            lop_hoc_id: item.lop_hoc_id,
            hoc_vien_id: item.hoc_vien_id
        }
    }).then((response) => {
        let data = response.data;
        listHocPhiDaDong.value = data;
        openPopupXemHocPhi.value = true;
    });
}

function xoaLop(item) {
    Swal.fire({
        title: 'Bạn có chắc muốn xoá?',
        showDenyButton: true,
        confirmButtonText: 'Đồng ý',
        denyButtonText: `Huỷ`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            router.post(route('hoc-vien.xoa-lop'), {
                id: item.lop_hoc_vien_id,
            });
        }
    })
}

</script>
<template>
    <section>
        <div class="flex justify-between mb-3">
            <h3 class="text-xl font-semibold">Lớp học</h3>
            <div class="flex space-x-1">
                <DangKyLop :list-lop-chua-hoc="listLopChuaHoc" :hoc-vien="hocVien" />
                <DongHocPhi :list-lop-dang-hoc="listLopDangHoc" :hoc-vien="hocVien" />
            </div>

        </div>

        <MyTable
            :rows="listLopHocVien"
            :columns="columns"
        >
            <template #cot_id="{ item }">{{ item.lop_hoc_vien_id }}</template>
            <template #cot_ten="{ item }">
                <Link :href="route('lop-hoc.edit', item.id)">{{ item.ten }}</Link>
            </template>
            <template #actions="{item}">
                <a href="#" @click.prevent="xemHocPhi(item)">Xem học phí</a>
                <a href="#" @click.prevent="xoaLop(item)" class="ml-3 text-red-500">Xoá</a>
            </template>
        </MyTable>

        <Modal :show="openPopupXemHocPhi" @close="openPopupXemHocPhi = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-3">
                    Lịch sử đóng học phí ({{ xemHocPhiLop }})
                </h2>
                <MyTable
                    :rows="listHocPhiDaDong"
                    :columns="hocPhiTableColumns"
                    :has-actions="false"
                >
                <template #cot_khoa="{ item }">
                    {{item.thang}}/{{ item.nam }}
                </template>
            </MyTable>
            </div>
        </Modal>
    </section>
</template>