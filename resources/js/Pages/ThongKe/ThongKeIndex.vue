<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import ChartTopHocVienTheoHocPhi from './Partials/ChartTopHocVienTheoHocPhi.vue';
import ChartTongHocPhiTheoLop from './Partials/ChartTongHocPhiTheoLop.vue';
import ChartTongHocPhiTheoNam from './Partials/ChartTongHocPhiTheoNam.vue';
import ChartSoHocVienTheoLop from './Partials/ChartSoHocVienTheoLop.vue';

const props = defineProps([]);

</script>

<template>
    <Head title="Tổng quan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tổng quan</h2>
        </template>
        <div class="md:grid grid-cols-2 gap-3">
            <Card>
                <div class="flex justify-between mb-3">
                    <h3 class="text-xl font-semibold">Tổng học phí</h3>
                </div>

                <ChartTongHocPhiTheoNam />
            </Card>

            <Card>
                <div class="flex justify-between mb-3">
                    <h3 class="text-xl font-semibold">Tổng học phí theo lớp</h3>
                </div>

                <ChartTongHocPhiTheoLop />
            </Card>

            <Card>
                <div class="flex justify-between mb-3">
                    <h3 class="text-xl font-semibold">Số học viên theo lớp</h3>
                </div>

                <ChartSoHocVienTheoLop />
            </Card>

            <Card>
                <div class="flex justify-between mb-3">
                    <h3 class="text-xl font-semibold">Top học viên theo học phí</h3>
                </div>

                <ChartTopHocVienTheoHocPhi />
            </Card>
        </div>

    </AuthenticatedLayout>
</template>
