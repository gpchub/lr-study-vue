
<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: [Array, String, Number],
        required: false,
        default: null,
    },

    placeholder: {
        type: String
    },

    options: {
        type: Array
    },

    optionValue: {
        type: String,
        default: 'value',
    },

    optionLabel: {
        type: String,
        default: 'label'
    }
});

const emit = defineEmits(['update:modelValue', 'change']);

const select = ref(null);

const proxySelected = computed({
    get() {
        return props.modelValue;
    },

    set(val) {
        emit("update:modelValue", val);
    },
});

const onChange = () => {
    emit('change');
}
</script>

<template>
    <select
        :id="id"
        ref="select"
        class="border-gray-300 focus:border-gray-200 focus:ring-gray-200 rounded-md shadow-sm"
        v-model="proxySelected"
        @change="onChange"
    >
        <option v-if="placeholder" :value="null">{{ placeholder }}</option>
        <option v-for="opt in options" :key="opt[optionValue]" :value="opt[optionValue]">{{ opt[optionLabel] }}</option>
    </select>
</template>