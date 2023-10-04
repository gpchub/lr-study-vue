<script setup>

</script>

<template>
    <div v-if="$page.props.flash.message"
    role="alert"
        class="p-4 mb-4 text-sm rounded-lg"
        :class="[$page.props.flash.status == 'error' ? 'text-red-800 bg-red-50' : 'text-green-800 bg-green-50']"
    >
        {{ $page.props.flash.message }}
    </div>
</template>