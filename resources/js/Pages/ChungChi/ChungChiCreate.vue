<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import MyTable from '@/Components/MyTable.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
//import Editor from '@tinymce/tinymce-vue'
import CKEditor from '@ckeditor/ckeditor5-vue';

import { ref, onMounted } from 'vue';

const props = defineProps([

]);

const form = useForm({
    ten: '',
    mo_ta: '',
});

const ckeditor = CKEditor.component;
const editor = ClassicEditor;
const editorConfig = {
    heading: {
        options: [
            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading2' },
        ]
    },
    toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'fontColor', '|', 'sourceEditing' ],
};

</script>

<template>
    <Head title="Thêm chứng chỉ" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thông tin chứng chỉ</h2>
        </template>

        <div class="md:grid grid-cols-2 gap-3">
            <Card>
                <form @submit.prevent="form.post(route('chung-chi.store'))" class="space-y-6 my-form">
                    <div>
                        <InputLabel for="ten" value="Tên" />
                        <TextInput id="ten" type="text" class="mt-1 block w-full" v-model="form.ten" required autofocus />
                        <InputError class="mt-2" :message="form.errors.ten" />
                    </div>

                    <div>
                        <InputLabel for="mo_ta" value="Mô tả" />
                        <!-- <textarea v-model="form.mo_ta" class="w-full" rows="10"/> -->
                        <!-- <Editor
                            v-model="form.mo_ta"
                            :init="{
                                plugins: 'lists link image table code help wordcount',
                                branding: false,
                                promotion: false,
                                toolbar: 'undo redo | styles | bold italic | link | bullist'
                            }"
                        /> -->
                        <ckeditor :editor="editor" v-model="form.mo_ta" :config="editorConfig"></ckeditor>
                        <InputError class="mt-2" :message="form.errors.mo_ta" />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">Lưu lại</PrimaryButton>
                    </div>
                </form>
            </Card>
        </div>

    </AuthenticatedLayout>
</template>
