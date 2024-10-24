<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    characters: {
        required: true,
        type: Array
    }
});
</script>


<template>
    <Head title="My Characters" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create Character
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="p-4 sm:rounded-lg sm:p-8">
                    Your characters here.
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
