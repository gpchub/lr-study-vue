<script setup>
import { ref, computed } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import MyTable from '@/Components/MyTable.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import DangKyThi from './DangKyThi.vue';
import Swal from 'sweetalert2';

const props = defineProps([
    'hocVien',
    'listLichThiHocVien',
    'listLichThiHocVienIds',
    'listLichThi',
]);

const listChuaThi = computed(() => {
    return props.listLichThi.filter(x => !props.listLichThiHocVienIds.includes(x.id));
});

const lichThiTableColumns = [
    { title: 'ID', field: 'id' },
    { title: 'Chứng chỉ', field: 'chung_chi' },
    { title: 'Ngày thi', field: 'ngay_thi' },
    { title: 'Địa điểm', field: 'dia_diem' },
    { title: 'Tình trạng', field: 'tinh_trang_text' },
    { title: 'Kết quả', field: 'ket_qua_text' },
];

const openPopupLichThi = ref(false);
let editLichThi = {};
const formLichThi = useForm({
    lich_thi_hoc_vien_id: 0,
    tinh_trang: 0,
    ket_qua: 0,
});

function suaLichThi(item)
{
    editLichThi = item;
    formLichThi.lich_thi_hoc_vien_id = item.lich_thi_hoc_vien_id;
    formLichThi.tinh_trang = item.tinh_trang ?? 0;
    formLichThi.ket_qua = item.ket_qua ?? 0;

    openPopupLichThi.value = true;
}

function submitLichThi() {
    formLichThi.post(route('hoc-vien.update-lich-thi'), {
        onSuccess: () => {
            formLichThi.reset();
            openPopupLichThi.value = false;
        }
    });
}

function xoaLichThi(item) {
    Swal.fire({
        title: 'Bạn có chắc muốn xoá?',
        showDenyButton: true,
        confirmButtonText: 'Đồng ý',
        denyButtonText: `Huỷ`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            router.post(route('hoc-vien.xoa-lich-thi'), {
                id: item.lich_thi_hoc_vien_id,
            });
        }
    })
}

</script>

<template>

    <section>
        <div class="flex justify-between mb-3 mt-6">
            <h3 class="text-xl font-semibold">Chứng chỉ</h3>
            <div class="space-x-1">
                <DangKyThi :hoc-vien="hocVien" :list-chua-thi="listChuaThi" />
            </div>
        </div>
        <MyTable
            :rows="listLichThiHocVien"
            :columns="lichThiTableColumns"
        >
            <template #cot_id="{ item }">{{ item.lich_thi_hoc_vien_id }}</template>
            <template #cot_chung_chi="{ item }">
                <Link :href="route('chung-chi.edit', item.chung_chi_id)">{{ item.ten_chung_chi }}</Link>
            </template>
            <template #actions="{item}">
                <a href="#" @click.prevent="suaLichThi(item)" class="text-blue-500">Sửa</a>
                <a href="#" @click.prevent="xoaLichThi(item)" class="ml-3 text-red-500">Xoá</a>
            </template>
        </MyTable>

        <Modal :show="openPopupLichThi" @close="openPopupLichThi = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-3">
                    Sửa thông tin thi chứng chỉ
                </h2>

                <form @submit.prevent="submitLichThi" class="space-y-6">
                    <div> <strong>Chứng chỉ: </strong> {{ editLichThi.ten_chung_chi }}</div>
                    <div> <strong>Ngày thi: </strong> {{ editLichThi.ngay_thi }}</div>
                    <div> <strong>Địa điểm: </strong> {{ editLichThi.dia_diem }}</div>
                    <div>
                        <InputLabel for="tinh_trang" value="Tình trạng" />
                        <select class="mt-1 block w-full form-select" v-model="formLichThi.tinh_trang">
                            <option value="0">Chưa thi</option>
                            <option value="1">Đã thi</option>
                        </select>
                        <InputError class="mt-2" :message="formLichThi.errors.tinh_trang" />
                    </div>

                    <div>
                        <InputLabel for="ket_qua" value="Kết quả" />
                        <select class="mt-1 block w-full form-select" v-model="formLichThi.ket_qua">
                            <option value="0">Chưa có</option>
                            <option value="1">Đạt</option>
                            <option value="2">Không đạt</option>
                        </select>
                        <InputError class="mt-2" :message="formLichThi.errors.ket_qua" />
                    </div>

                    <div class="flex space-x-1">
                        <PrimaryButton :disabled="formLichThi.processing">Lưu lại</PrimaryButton>
                        <SecondaryButton @click="openPopupLichThi = false"> Huỷ </SecondaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </section>
</template>