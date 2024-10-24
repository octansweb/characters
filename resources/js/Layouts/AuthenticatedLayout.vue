<script setup>
import { onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useToast } from "vue-toastification";

const showingSidebar = ref(false);
const toast = useToast();
const { props } = usePage(); // Access page props

console.log('CurrentProps', { props });

function toggleSidebar() {
    showingSidebar.value = !showingSidebar.value;
}

function closeSidebar() {
    showingSidebar.value = false;
}

onMounted(() => {
    // Check if there is a flash message
    if (props.flash && props.flash.success) {
        console.log('Sending successs flash message');
        toast.success(props.flash.success); // Display the success toast
    }

    // If you have other types of flash messages
    if (props.flash && props.flash.error) {
        toast.error(props.flash.error);
    }

    // For generic messages
    if (props.flash && props.flash.message) {
        toast(props.flash.message);
    }
});
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
                            <Link :href="route('characters.create')"
                                class="flex items-center p-2 text-sm text-gray-900 rounded-lg hover:bg-gray-100">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                            </svg>

                            <span class="ms-3">Create</span>
                            </Link>
                        </li>


                        <li>
                            <Link :href="route('characters.index')"
                                class="flex items-center p-2 text-sm text-gray-900 rounded-lg hover:bg-gray-100">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>


                            <span class="ms-3">My Characters</span>
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
