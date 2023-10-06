<template>
    <div>
        <PrimaryButton type="button" @click="openPopupDongHocPhi = true">Đóng học phí</PrimaryButton>

        <Modal :show="openPopupDongHocPhi" @close="openPopupDongHocPhi = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-3">
                    Đóng học phí
                </h2>

                <form @submit.prevent="submitFormDongHocPhi" class="space-y-6">
                    <div>
                        <InputLabel for="lop_hoc_id" value="Lớp đang học" />
                        <select class="mt-1 block w-full form-select" v-model="formDongHocPhi.lop_hoc_id">
                            <option :value="null">--- Chọn lớp học ---</option>
                            <option v-for="item in listLopDangHoc" :value="item.id">{{ item.ten }}</option>
                        </select>
                        <InputError class="mt-2" :message="formDongHocPhi.errors.lop_hoc_id" />
                    </div>

                    <div class="md:grid grid-cols-2 gap-2">
                        <div>
                            <InputLabel for="thang" value="Tháng" />
                            <select id="thang" class="mt-1 block w-full form-select" v-model="formDongHocPhi.thang">
                                <option v-for="item in 12" :value="item">{{ item }}</option>
                            </select>
                            <InputError class="mt-2" :message="formDongHocPhi.errors.thang" />
                        </div>
                        <div>
                            <InputLabel for="nam" value="Năm" />
                            <select id="nam" class="mt-1 block w-full form-select" v-model="formDongHocPhi.nam">
                                <option v-for="item in listYears" :value="item">{{ item }}</option>
                            </select>
                            <InputError class="mt-2" :message="formDongHocPhi.errors.nam" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="so_tien" value="Số tiền" />
                        <TextInput id="so_tien" type="number" class="mt-1 block w-full" v-model="formDongHocPhi.so_tien" required  />
                        <InputError class="mt-2" :message="formDongHocPhi.errors.so_tien" />
                    </div>

                    <div>
                        <InputLabel for="ngay_dong" value="Ngày đóng" />
                        <MyDatepicker id="ngay_dong" class="mt-1 block w-full" v-model="formDongHocPhi.ngay_dong" required  />
                        <InputError class="mt-2" :message="formDongHocPhi.errors.ngay_dong" />
                    </div>

                    <div class="flex space-x-1">
                        <PrimaryButton :disabled="formDongHocPhi.processing">Lưu lại</PrimaryButton>
                        <SecondaryButton @click="openPopupDongHocPhi = false"> Huỷ </SecondaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import MyDatepicker from '@/Components/MyDatepicker.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps([
    'listLopDangHoc',
    'hocVien'
]);

const openPopupDongHocPhi = ref(false);

const today = new Date();
const formDongHocPhi = useForm({
    hoc_vien_id: props.hocVien.id,
    lop_hoc_id: null,
    ngay_dong: `${today.getFullYear()}/${today.getMonth() + 1}/${today.getDate()}`,
    thang: today.getMonth() + 1,
    nam: today.getFullYear(),
    so_tien: '',
});

function submitFormDongHocPhi() {
    formDongHocPhi.post(route('hoc-vien.dong-hoc-phi'), {
        onSuccess: () => {
            formDongHocPhi.reset();
            openPopupDongHocPhi.value = false;
        }
    });
}

// tạo mảng dãy số liên tục
// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/from#sequence_generator_range
const range = (start, stop, step) =>
  Array.from({ length: (stop - start) / step + 1 }, (_, i) => start + i * step);

const listYears = range(1950, new Date().getFullYear(), 1);

</script>