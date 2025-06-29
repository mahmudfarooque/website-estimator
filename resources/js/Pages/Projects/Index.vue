<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    projects: Array,
});
</script>

<template>
    <Head title="My Projects" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Projects</h2>
                <Link :href="route('projects.create')">
                    <PrimaryButton>Create New Project</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <ul v-if="projects.length > 0" role="list" class="divide-y divide-gray-200">
                        <li v-for="project in projects" :key="project.id">
                            <Link :href="route('projects.summary', project.id)" class="block hover:bg-gray-50">
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <p class="text-md font-medium text-indigo-600 truncate">{{ project.name }}</p>
                                        <div class="ml-2 flex-shrink-0 flex">
                                            <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                               :class="{
                                                   'bg-yellow-100 text-yellow-800': project.status === 'design',
                                                   'bg-blue-100 text-blue-800': project.status === 'draft',
                                                   'bg-green-100 text-green-800': project.status === 'invoiced',
                                               }"
                                            >
                                                {{ project.status }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2 sm:flex sm:justify-between">
                                        <div class="sm:flex">
                                            <p class="flex items-center text-sm text-gray-500">
                                                </p>
                                        </div>
                                        <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                              <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                            <p>
                                                Created on <time :datetime="project.created_at">{{ new Date(project.created_at).toLocaleDateString() }}</time>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        </li>
                    </ul>
                     <div v-else class="p-6 text-center text-gray-500">
                        <h3 class="text-lg font-medium">No projects yet!</h3>
                        <p class="mt-1">Get started by creating your first estimate.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>