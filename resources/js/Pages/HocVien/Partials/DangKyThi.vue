<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import MyDatepicker from '@/Components/MyDatepicker.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps([
    'hocVien',
    'listChuaThi',
]);

const openPopupDangKyThi = ref(false);
const formDangKyThi = useForm({
    hoc_vien_id: props.hocVien.id,
    lich_thi_id: 0,
});

function submitDangKyThi() {
    formDangKyThi.post(route('hoc-vien.dang-ky-thi'), {
        onSuccess: () => {
            formDangKyThi.reset();
            openPopupDangKyThi.value = false;
        }
    });
}

</script>

<template>
    <PrimaryButton type="button" @click="openPopupDangKyThi = true">Đăng ký thi</PrimaryButton>

    <Modal :show="openPopupDangKyThi" @close="openPopupDangKyThi = false">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-3">
                Đăng ký thi
            </h2>

            <form @submit.prevent="submitDangKyThi" class="space-y-6">
                <div>
                    <InputLabel for="lich_thi_id" value="Chọn chứng chỉ và ngày thi" />
                    <select class="mt-1 block w-full form-select" v-model="formDangKyThi.lich_thi_id">
                        <option value="0">--- Chọn lịch thi ---</option>
                        <option v-for="item in listChuaThi" :value="item.id">{{ item.chung_chi.ten }} - {{ item.ngay_thi }} </option>
                    </select>
                    <InputError class="mt-2" :message="formDangKyThi.errors.lich_thi_id" />
                </div>

                <div class="flex space-x-1">
                    <PrimaryButton :disabled="formDangKyThi.processing">Lưu lại</PrimaryButton>
                    <SecondaryButton @click="openPopupDangKyThi = false"> Huỷ </SecondaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>