<script setup>
import { ref, reactive, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    websitePackages: Array,
    hostingPackages: Array,
    emailPackages: Array,
});

// --- STATE MANAGEMENT ---
const currentStep = ref(1);
const domainChoice = ref(null);
const domainName = ref('');
const isCheckingDomain = ref(false);
const isDomainRegistered = ref(null);
const domainCheckError = ref(null);

const estimatorForm = useForm({
    projectName: '',
    website_package_id: null,
    hosting_package_id: null,
    email_package_id: null,
});

// --- METHODS ---
const selectDomainChoice = (choice) => {
    domainChoice.value = choice;
    domainName.value = '';
    isDomainRegistered.value = null;
    domainCheckError.value = null;
};

const checkDomain = async () => {
    if (domainName.value.length < 4 || !domainName.value.includes('.')) {
        return;
    }

    isCheckingDomain.value = true;
    isDomainRegistered.value = null;
    domainCheckError.value = null;

    try {
        // THE FIX: Use JavaScript's .replace() method instead of PHP's preg_replace()
        const domainToCheck = domainName.value.replace(/^(https?:\/\/)?(www\.)?/i, '').split('/')[0];

        const response = await axios.post(route('domain.check'), { domain: domainToCheck });
        isDomainRegistered.value = response.data.is_registered;
    } catch (error) {
        console.error("Domain check failed:", error);
        if (error.response && error.response.data && error.response.data.error) {
            domainCheckError.value = error.response.data.error;
        } else {
            domainCheckError.value = 'An unexpected error occurred. Please try again.';
        }
    } finally {
        isCheckingDomain.value = false;
    }
};

const proceedToEstimator = () => {
    estimatorForm.projectName = domainName.value;
    currentStep.value = 2;
};

const submitEstimator = () => {
    estimatorForm.post(route('projects.store'));
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Create New Project" />

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 md:p-8">

                    <!-- STEP 1: DOMAIN SELECTION -->
                    <div v-if="currentStep === 1">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Let's start with your domain name.</h2>
                        <p class="text-gray-600 mb-6">This will become the title of your project.</p>

                        <div class="mb-6">
                            <InputLabel value="Do you have an existing domain name?" class="mb-2" />
                            <div class="flex space-x-4">
                                <button @click="selectDomainChoice('yes')" :class="{'ring-2 ring-indigo-500 bg-indigo-100': domainChoice === 'yes'}" class="w-full text-left p-4 border rounded-lg hover:border-indigo-400 transition-all">Yes, I own a domain.</button>
                                <button @click="selectDomainChoice('no')" :class="{'ring-2 ring-indigo-500 bg-indigo-100': domainChoice === 'no'}" class="w-full text-left p-4 border rounded-lg hover:border-indigo-400 transition-all">No, I need a new one.</button>
                            </div>
                        </div>

                        <!-- If user has a domain -->
                        <div v-if="domainChoice === 'yes'" class="mt-4 border-t pt-6">
                            <InputLabel for="existing_domain" value="Enter your existing domain name:" />
                            <TextInput id="existing_domain" type="text" class="mt-1 block w-full" v-model="domainName" @blur="checkDomain" placeholder="e.g., mybusiness.com" />
                            <p v-if="isCheckingDomain" class="text-sm text-gray-500 mt-2">Checking...</p>
                            <p v-if="isDomainRegistered === true" class="text-sm text-green-600 mt-2">✓ Great, we can see this domain is registered.</p>
                            <p v-if="isDomainRegistered === false" class="text-sm text-red-600 mt-2">✗ We can't find this domain. Please double-check the spelling.</p>
                            <p v-if="domainCheckError" class="text-sm text-red-600 mt-2">✗ {{ domainCheckError }}</p>
                        </div>

                        <!-- If user needs a domain -->
                        <div v-if="domainChoice === 'no'" class="mt-4 border-t pt-6">
                            <InputLabel for="new_domain" value="Search for a new domain name:" />
                            <TextInput id="new_domain" type="text" class="mt-1 block w-full" v-model="domainName" @blur="checkDomain" placeholder="e.g., mynewbusiness.com" />
                             <p v-if="isCheckingDomain" class="text-sm text-gray-500 mt-2">Checking availability...</p>
                             <p v-if="isDomainRegistered === false" class="text-sm text-green-600 mt-2">✓ Good news! This domain appears to be available!</p>
                             <p v-if="isDomainRegistered === true" class="text-sm text-red-600 mt-2">✗ Sorry, this domain is already taken. Try another name.</p>
                             <p v-if="domainCheckError" class="text-sm text-red-600 mt-2">✗ {{ domainCheckError }}</p>
                        </div>

                        <div class="mt-8 text-right">
                             <PrimaryButton @click="proceedToEstimator" :disabled="isCheckingDomain || domainName.length < 4 || isDomainRegistered === null || (domainChoice === 'yes' && isDomainRegistered === false) || (domainChoice === 'no' && isDomainRegistered === true)">
                                Continue to Estimator
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- NEW CONSOLIDATED STEP 2: ALL PACKAGES -->
                    <div v-if="currentStep === 2" class="space-y-12">
                        <!-- Website Section -->
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">1. What kind of website do you need?</h2>
                            <p class="text-gray-600 mb-6">Selected Domain: <span class="font-bold">{{ estimatorForm.projectName }}</span></p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div v-for="pkg in websitePackages" :key="pkg.id" @click="estimatorForm.website_package_id = pkg.id" class="p-6 border rounded-lg cursor-pointer transition-all" :class="{ 'border-indigo-600 ring-2 ring-indigo-300 shadow-lg': estimatorForm.website_package_id === pkg.id }">
                                    <h3 class="font-bold text-lg">{{ pkg.name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1 h-12">{{ pkg.description }}</p>
                                    <p class="font-extrabold text-2xl text-gray-800 mt-4">${{ pkg.price }}</p>
                                </div>
                            </div>
                        </div>

                         <!-- Hosting Section -->
                        <div class="border-t pt-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">2. Choose a Hosting Plan</h2>
                            <p class="text-gray-600 mb-6">Our hosting is fast, secure, and reliable.</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div v-for="pkg in hostingPackages" :key="pkg.id" @click="estimatorForm.hosting_package_id = pkg.id" class="p-6 border rounded-lg cursor-pointer transition-all" :class="{ 'border-indigo-600 ring-2 ring-indigo-300 shadow-lg': estimatorForm.hosting_package_id === pkg.id }">
                                    <h3 class="font-bold text-lg">{{ pkg.name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1 h-12">{{ pkg.description }}</p>
                                    <p class="font-extrabold text-2xl text-gray-800 mt-4">${{ pkg.price }}</p>
                                 </div>
                            </div>
                        </div>

                         <!-- Email Section -->
                        <div class="border-t pt-8">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">3. Select an Email Hosting Plan</h2>
                            <p class="text-gray-600 mb-6">Get professional email addresses for your domain.</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div v-for="pkg in emailPackages" :key="pkg.id" @click="estimatorForm.email_package_id = pkg.id" class="p-6 border rounded-lg cursor-pointer transition-all" :class="{ 'border-indigo-600 ring-2 ring-indigo-300 shadow-lg': estimatorForm.email_package_id === pkg.id }">
                                    <h3 class="font-bold text-lg">{{ pkg.name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1 h-12">{{ pkg.description }}</p>
                                    <p class="font-extrabold text-2xl text-gray-800 mt-4">${{ pkg.price }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="mt-12 flex justify-between border-t pt-8">
                            <button @click="currentStep = 1" class="px-6 py-3 bg-gray-600 text-white font-bold rounded-lg hover:bg-gray-700 transition-all">Back to Domain</button>
                            <PrimaryButton @click="submitEstimator" :disabled="!estimatorForm.website_package_id || !estimatorForm.hosting_package_id || !estimatorForm.email_package_id">
                                Finish & Save Project
                            </PrimaryButton>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
