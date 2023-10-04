<script setup>
import { computed, ref } from 'vue';
import Pagination from './Pagination.vue';

const props = defineProps({
    rows: {
        type: Array,
        default: []
    },
    columns: {
        type: Array,
        default: [],
        /**
         * {
         *      title: '',
         *      field: '',
         * }
         */
    },
    links: {
        type: Array,
        default: []
    },
    currentPage: {
        type: Number,
        default: 1,
    },
    hasActions: {
        type: Boolean,
        default: true,
    },
    hasCheckboxes: {
        type: Boolean,
        default: false,
    }
});

const emits = defineEmits(['checkChanged']);

const checkedIds = ref([]);

const checkAll = computed({
    get() {
        return checkedIds.value.length == props.rows.length;
    },
    // setter
    set(newValue) {
        let selected = [];

        if (newValue) {
            selected = props.rows.map((item) => item.id)
        }

        checkedIds.value = selected;
    }
});

function getSelectedIds()
{
    return checkedIds.value;
}

defineExpose({
    getSelectedIds,
});

</script>

<template>
    <div class="my-table">
        <div class="border rounded-lg overflow-hidden mb-3 border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-indigo-50">
                    <tr>
                        <th v-if="hasCheckboxes">
                            <input type="checkbox" v-model="checkAll">
                        </th>
                        <th v-for="item in columns" class="px-4 py-3 text-left uppercase" >{{ item.title }}</th>
                        <th v-if="hasActions"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="item in rows">
                        <td v-if="hasCheckboxes" class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" :value="item.id" v-model="checkedIds"/>
                        </td>
                        <td v-for="col in columns" class="px-4 py-3 whitespace-nowrap">
                            <slot :name="'cot_' + col.field" :item="item">
                                {{ item[col.field] }}
                            </slot>
                        </td>
                        <td  v-if="hasActions" class="px-4 py-3 whitespace-nowrap text-right">
                            <slot name="actions" class="abvc" :id="item.id" :item="item"></slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination v-if="links.length > 3" :links="links" :current-page="currentPage"></Pagination>
    </div>
</template>