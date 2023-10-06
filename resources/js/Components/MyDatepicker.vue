<script setup>
import { ref, onMounted } from 'vue';
import flatpickr from 'flatpickr';

const props = defineProps({
    modelValue: String,
    hasTimePicker: {
        type: Boolean,
        default: false,
    },
    minDate: {
        type: [String, Date],
        default: null,
    }
})
defineEmits(['update:modelValue'])

const inputElement = ref(null);

onMounted(() => {
    let options = {
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
        minDate: props.minDate,
    };

    if (props.hasTimePicker) {
        options.enableTime = true;
        options.altFormat = 'd/m/Y H:i';
        options.dateFormat = 'Y-m-d H:i';
    }

    flatpickr(inputElement.value, options);
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