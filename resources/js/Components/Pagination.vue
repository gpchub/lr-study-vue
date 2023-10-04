<script setup>
import {  Link } from '@inertiajs/vue3';

defineProps(['links', 'currentPage']);

</script>
<template>
    <nav>
        <ul class="inline-flex -space-x-px text-sm h-10">
            <li v-for="(item, index) in links" >
                <span v-if="item.label == currentPage"
                    class="flex items-center justify-center px-3 h-8 text-blue-600 border border-gray-200 bg-indigo-50 hover:bg-indigo-100 hover:text-blue-700"
                    :class="{'rounded-l-lg' : index == 0, 'rounded-r-lg' : index == links.length - 1 }"
                    v-html="item.label"
                ></span>
                <Link v-else-if="item.url"
                    :href="item.url"
                    v-html="item.label"
                    class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-200 hover:bg-gray-100 hover:text-gray-700"
                    :class="{'rounded-l-lg' : index == 0, 'rounded-r-lg' : index == links.length - 1 }"
                />
                <span v-else
                    v-html="item.label"
                    class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-200 hover:bg-gray-100 hover:text-gray-700"
                    :class="{'rounded-l-lg' : index == 0, 'rounded-r-lg' : index == links.length - 1 }"
                ></span>
            </li>
        </ul>
    </nav>
</template>