<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import MyDatepicker from '@/Components/MyDatepicker.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps([
    'listLopChuaHoc',
    'hocVien'
]);

const openPopupDangKyLop = ref(false);

const formDangKyLop = useForm({
    hoc_vien_id: props.hocVien.id,
    lop_hoc_id: null,
    ngay_bat_dau: '',
});

function submitFormDangKyLop() {
    formDangKyLop.post(route('hoc-vien.dang-ky-lop'), {
        onSuccess: () => {
            formDangKyLop.reset();
            openPopupDangKyLop.value = false;
        }
    });
}

</script>

<template>
    <div>
        <PrimaryButton type="button" @click="openPopupDangKyLop = true">Đăng ký lớp</PrimaryButton>
        <Modal :show="openPopupDangKyLop" @close="openPopupDangKyLop = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-3">
                    Đăng ký lớp Modal
                </h2>
                <form @submit.prevent="submitFormDangKyLop" class="space-y-6">
                    <div>
                        <InputLabel for="lop_hoc_id" value="Lớp học" />
                        <select class="mt-1 block w-full form-select" v-model="formDangKyLop.lop_hoc_id">
                            <option :value="null">--- Chọn lớp học ---</option>
                            <option v-for="item in listLopChuaHoc" :value="item.id">{{ item.ten }}</option>
                        </select>
                        <InputError class="mt-2" :message="formDangKyLop.errors.lop_hoc_id" />
                    </div>
                    <div>
                        <InputLabel for="ngay_bat_dau" value="Ngày bắt đầu" />
                        <MyDatepicker id="ngay_bat_dau" class="mt-1 block w-full" v-model="formDangKyLop.ngay_bat_dau" required  />
                        <InputError class="mt-2" :message="formDangKyLop.errors.ngay_bat_dau" />
                    </div>
                    <div class="flex space-x-1">
                        <PrimaryButton :disabled="formDangKyLop.processing">Lưu lại</PrimaryButton>
                        <SecondaryButton @click="openPopupDangKyLop = false"> Huỷ </SecondaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>