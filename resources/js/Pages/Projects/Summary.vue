<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    project: Object,
});

// Get the entire flash object from the page props
const flash = computed(() => usePage().props.flash);

// Calculate the total price by adding up the prices of all selected packages.
const totalPrice = computed(() => {
    if (!props.project || !props.project.packages) return 0;
    return props.project.packages.reduce((total, pkg) => total + parseFloat(pkg.price), 0);
});

// A helper function to easily find a package of a specific type.
const getPackageByType = (type) => {
    if (!props.project || !props.project.packages) return null;
    return props.project.packages.find(pkg => pkg.type === type);
};
</script>

<template>
    <Head :title="`Summary for ${project.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Project Summary & Invoice</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Success Message Banner -->
                <!-- THE FIX: Use a more robust check to ensure 'flash' exists before accessing 'success' -->
                <div v-if="flash && flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ flash.success }}</span>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 space-y-8">

                        <!-- Project Header -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Project: {{ project.name }}</h3>
                            <p class="text-sm text-gray-500">Created on: {{ new Date(project.created_at).toLocaleDateString() }}</p>
                            <p class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                               :class="{
                                   'bg-yellow-100 text-yellow-800': project.status === 'design',
                                   'bg-blue-100 text-blue-800': project.status === 'draft',
                                   'bg-green-100 text-green-800': project.status === 'submitted',
                               }">
                                {{ project.status }}
                            </p>
                        </div>

                        <!-- Selected Packages & Pricing -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-gray-900">Selected Services</h3>
                                <Link :href="route('projects.create')" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Change Selections</Link>
                            </div>
                            <dl class="mt-4 space-y-4">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Website: <span class="font-medium text-gray-800">{{ getPackageByType('website')?.name || 'N/A' }}</span></dt>
                                    <dd class="text-sm font-medium text-gray-900">${{ parseFloat(getPackageByType('website')?.price || 0).toFixed(2) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Hosting: <span class="font-medium text-gray-800">{{ getPackageByType('hosting')?.name || 'N/A' }}</span></dt>
                                    <dd class="text-sm font-medium text-gray-900">${{ parseFloat(getPackageByType('hosting')?.price || 0).toFixed(2) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Email: <span class="font-medium text-gray-800">{{ getPackageByType('email')?.name || 'N/A' }}</span></dt>
                                    <dd class="text-sm font-medium text-gray-900">${{ parseFloat(getPackageByType('email')?.price || 0).toFixed(2) }}</dd>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 pt-4 mt-4">
                                    <dt class="text-base font-bold text-gray-900">Total Estimate</dt>
                                    <dd class="text-base font-bold text-gray-900">${{ totalPrice.toFixed(2) }}</dd>
                                </div>
                            </dl>
                        </div>

                         <!-- Uploaded Files -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-gray-900">Uploaded Files</h3>
                                <Link :href="route('projects.design', project.id)" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Edit Uploads</Link>
                            </div>
                            <ul v-if="project.uploads && project.uploads.length > 0" role="list" class="mt-4 border border-gray-200 rounded-md divide-y divide-gray-200">
                                <li v-for="upload in project.uploads" :key="upload.id" class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                    <div class="w-0 flex-1 flex items-center">
                                        <img v-if="upload.mime_type && upload.mime_type.startsWith('image/')" :src="upload.public_url" class="h-10 w-10 rounded-md object-cover flex-shrink-0" alt="File thumbnail">
                                        <div v-else class="h-10 w-10 flex-shrink-0 flex items-center justify-center bg-gray-100 rounded-md border">
                                            <svg class="h-6 w-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                        </div>
                                        <span class="ml-3 flex-1 w-0 truncate">{{ upload.original_filename }}</span>
                                    </div>
                                </li>
                            </ul>
                            <p v-else class="mt-2 text-sm text-gray-500">No files were uploaded for this project.</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="border-t border-gray-200 pt-6 flex justify-end">
                            <Link
                                :href="route('projects.order.submit', project.id)"
                                method="post"
                                as="button"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                :disabled="project.status === 'submitted'"
                                :class="{ 'opacity-50 cursor-not-allowed': project.status === 'submitted' }"
                            >
                                {{ project.status === 'submitted' ? 'Order Submitted' : 'Submit Order' }}
                            </Link>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
