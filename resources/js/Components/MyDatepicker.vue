<script setup>
import { ref, onMounted } from 'vue';
import flatpickr from 'flatpickr';

defineProps(['modelValue'])
defineEmits(['update:modelValue'])

const inputElement = ref(null);

onMounted(() => {
    flatpickr(inputElement.value, {
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
    });
});

</script>
<template>
    <input
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="inputElement"
        class="border-gray-300 focus:border-gray-200 focus:ring-gray-200 rounded-md shadow-sm"
    />
</template>