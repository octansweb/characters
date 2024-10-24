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
                My Characters
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Grid Layout for Cards -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
                    <!-- Character Card -->
                    <div
                        v-for="character in characters"
                        :key="character.id"
                        class="bg-white p-6 rounded-lg shadow"
                    >
                        <!-- Avatar Image -->
                        <img
                            v-if="character.avatar_url"
                            :src="character.avatar_url"
                            alt="Avatar"
                            class="h-32 w-32 rounded-full mx-auto object-cover"
                        />
                        <!-- Default Avatar if none provided -->
                        <div
                            v-else
                            class="h-32 w-32 rounded-full mx-auto bg-gray-200 flex items-center justify-center"
                        >
                            <span class="text-gray-500 text-3xl">
                                {{ character.name.charAt(0).toUpperCase() }}
                            </span>
                        </div>

                        <!-- Character Name -->
                        <h3 class="mt-4 text-xl font-semibold text-center text-gray-800">
                            {{ character.name }}
                        </h3>

                        <!-- Character Description -->
                        <p class="mt-2 text-gray-600 text-center">
                            {{ character.description || 'No description provided.' }}
                        </p>

                        <!-- Additional Details -->
                        <div class="mt-4">
                            <p class="text-sm text-gray-500">
                                Personality: {{ character.personality || 'Not specified.' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                Status: {{ character.is_public ? 'Public' : 'Private' }}
                            </p>
                        </div>
                    </div>
                    <!-- End of Character Card -->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
