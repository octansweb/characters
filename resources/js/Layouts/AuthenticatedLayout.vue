<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const showingSidebar = ref(false);

function toggleSidebar() {
    showingSidebar.value = !showingSidebar.value;
}

function closeSidebar() {
    showingSidebar.value = false;
}
</script>

<template>
    <div class="flex">
        <!-- Overlay -->
        <div v-if="showingSidebar" class="fixed inset-0 z-30 bg-black opacity-50 sm:hidden" @click="closeSidebar"></div>

        <!-- Sidebar -->
        <aside id="sidebar" :class="[
            'fixed top-0 left-0 z-40 w-64 h-full transition-transform transform bg-gray-50',
            showingSidebar ? 'translate-x-0' : '-translate-x-full',
            'sm:translate-x-0'
        ]" aria-label="Sidebar">
            <div class="h-full flex flex-col">
                <!-- Branding -->
                <div class="relative px-4 py-5">
                    <Link href="/" class="text-lg font-bold text-gray-800">
                    Characters
                    </Link>
                    <!-- Close button on mobile -->
                    <button @click="closeSidebar" class="absolute top-6 right-4 sm:hidden text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                        </svg>

                    </button>
                </div>

                <!-- Scrollable navigation and beta banner -->
                <div class="flex-1 overflow-y-auto">
                    <!-- Navigation -->
                    <ul class="space-y-2 px-3 py-4">
                        <li>
                            <Link href="#"
                                class="flex items-center p-2 text-sm text-gray-900 rounded-lg hover:bg-gray-100">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span class="ms-3">Create</span>
                            </Link>
                        </li>

                        <li>
                            <Link href="#"
                                class="flex items-center p-2 text-sm text-gray-900 rounded-lg hover:bg-gray-100">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>

                            <span class="ms-3">Explore</span>
                            </Link>
                        </li>
                        <!-- Add other navigation items here -->
                        <!-- ... -->
                    </ul>

                </div>

                <!-- Bottom fixed area -->
                <div class="px-3 py-4 flex-shrink-0">
                    <ul class="space-y-2">
                        <!-- Add other bottom items here -->
                        <!-- ... -->
                        <!-- Logout Button -->
                        <li>
                            <form method="POST" :action="route('logout')">
                                <!-- CSRF Token -->
                                <input type="hidden" name="_token" :value="csrfToken" />

                                <button type="submit"
                                    class="flex items-center w-full p-2 text-gray-900 rounded-lg hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M10 3H6a2 2 0 0 0-2 2v14c0 1.1.9 2 2 2h4M16 17l5-5-5-5M19.8 12H9" />
                                    </svg>
                                    <span class="ms-3">Log Out</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 sm:ml-64">
            <!-- Mobile hamburger button -->
            <button @click="toggleSidebar" type="button"
                class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                <span class="sr-only">Open sidebar</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                    <path clip-rule="evenodd" fill-rule="evenodd"
                        d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                    </path>
                </svg>
            </button>

            <!-- Content -->
            <div class="p-4">
                <!-- Your page content goes here -->
                <slot />
            </div>
        </div>
    </div>
</template>
