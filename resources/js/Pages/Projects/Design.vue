<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { router } from '@inertiajs/vue3';

// The project object (including its uploads) is passed as a prop from the controller.
const props = defineProps({
    project: Object,
});

// A computed property to easily access the list of uploads.
const uploads = computed(() => props.project.uploads);

// useForm helper for handling file uploads.
const form = useForm({
    file: null,
});

// State for the file preview before uploading.
const filePreview = ref(null);
const selectedFile = ref(null);

// This function is called when the user selects a file from the file input.
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (!file) {
        filePreview.value = null;
        selectedFile.value = null;
        return;
    }

    if (uploads.value.length >= 5) {
        alert('You cannot upload more than 5 files.');
        event.target.value = null;
        return;
    }

    selectedFile.value = file;

    if (file.type.startsWith('image/')) {
        filePreview.value = URL.createObjectURL(file);
    } else {
        filePreview.value = null;
    }
};

// This function is triggered by the "Upload File" button.
const uploadFile = () => {
    if (!selectedFile.value) {
        alert('Please choose a file to upload.');
        return;
    }

    form.file = selectedFile.value;

    form.post(route('projects.uploads.store', props.project.id), {
        onSuccess: () => {
            document.getElementById('file-upload').value = null;
            filePreview.value = null;
            selectedFile.value = null;
        },
        onError: (errors) => {
            alert(errors.file || 'An unknown error occurred during upload.');
        }
    });
};

// This function is called when the user clicks the "Remove" button.
const removeUpload = (uploadId) => {
    if (confirm('Are you sure you want to delete this file?')) {
        router.delete(route('projects.uploads.destroy', uploadId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Mockups for '${project.name}'`" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8">

                    <h1 class="text-2xl font-bold text-gray-800">Upload Your Mockup & Content</h1>
                    <p class="mt-2 text-gray-600 mb-6">Upload your designs, content documents, or any other relevant files. (Max 5 files, 5MB each).</p>

                    <!-- File Upload Input and Preview Section -->
                    <div class="border-t border-b border-gray-200 py-6">
                        <div class="flex items-start space-x-6">
                            <div class="flex-1">
                                <label for="file-upload" class="sr-only">Choose file</label>
                                <input type="file" @change="handleFileUpload" id="file-upload" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                                <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full mt-2">{{ form.progress.percentage }}%</progress>
                                <PrimaryButton @click="uploadFile" :disabled="!selectedFile || form.processing" class="mt-4">
                                    {{ form.processing ? 'Uploading...' : 'Upload Selected File' }}
                                </PrimaryButton>
                            </div>
                            <div class="w-32 h-32 flex-shrink-0 bg-gray-100 rounded-lg flex items-center justify-center border">
                                <img v-if="filePreview" :src="filePreview" class="max-h-full max-w-full object-contain rounded-lg">
                                <p v-else class="text-xs text-gray-500 text-center px-2">Image preview</p>
                            </div>
                        </div>
                    </div>


                    <!-- List of Already Uploaded Files -->
                    <div class="mt-8">
                        <h2 class="font-bold text-lg text-gray-700">Uploaded Files ({{ uploads.length }}/5)</h2>
                        <ul v-if="uploads.length > 0" role="list" class="divide-y divide-gray-200 border-t border-b border-gray-200 mt-4">
                            <!-- THE FIX IS IN THIS v-for LOOP -->
                            <li v-for="upload in uploads" :key="upload.id" class="flex items-center justify-between py-3 px-2">
                                <div class="flex items-center min-w-0">
                                    <!-- Show thumbnail for images -->
                                    <img v-if="upload.mime_type && upload.mime_type.startsWith('image/')" :src="upload.public_url" class="h-10 w-10 rounded-md object-cover flex-shrink-0" alt="File thumbnail">
                                    <!-- Show generic icon for other files -->
                                    <div v-else class="h-10 w-10 flex-shrink-0 flex items-center justify-center bg-gray-100 rounded-md border">
                                        <svg class="h-6 w-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ upload.original_filename }}</p>
                                        <p class="text-sm text-gray-500">({{ (upload.size / 1024).toFixed(2) }} KB)</p>
                                    </div>
                                </div>
                                <button @click="removeUpload(upload.id)" class="ml-4 text-sm font-medium text-red-600 hover:text-red-800 flex-shrink-0">Remove</button>
                            </li>
                        </ul>
                        <p v-else class="mt-4 text-sm text-gray-500">No files have been uploaded for this project yet.</p>
                    </div>

                    <!-- Navigation to next step -->
                     <div class="mt-8 pt-8 border-t flex justify-end">
                        <Link :href="route('projects.summary', project.id)">
                            <PrimaryButton>Continue to Summary</PrimaryButton>
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
