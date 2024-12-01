<template>
  <Head title="Welcome to CharactersHub" />

  <div class="relative min-h-screen bg-gradient-to-b from-gray-900 to-gray-800">
    <!-- Navigation -->
    <nav class="relative px-8 py-6">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="text-2xl font-bold text-white">CharactersHub</div>
        <div class="flex items-center gap-6">
          <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-gray-300 hover:text-white transition">
            Dashboard
          </Link>
          <template v-else>
            <Link :href="route('login')" class="text-gray-300 hover:text-white transition">
              Log in
            </Link>
            <Link :href="route('register')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 transition">
              Get Started
            </Link>
          </template>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative max-w-7xl mx-auto px-8 py-24">
      <div class="max-w-3xl">
        <h1 class="text-5xl font-bold text-white mb-6">
          Create, Chat, and Connect with AI Characters
        </h1>
        <p class="text-xl text-gray-300 mb-8">
          Bring your characters to life with advanced AI technology. Create unique personalities, engage in natural conversations, and experience a new way of storytelling.
        </p>
        <div class="flex gap-4">
          <Link :href="route('register')" class="bg-indigo-600 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-500 transition">
            Create Your Character
          </Link>
          <a href="#features" class="border border-gray-600 text-gray-300 px-6 py-3 rounded-lg text-lg font-semibold hover:border-gray-500 hover:text-white transition">
            Learn More
          </a>
        </div>
      </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="relative bg-gray-800 py-24">
      <div class="max-w-7xl mx-auto px-8">
        <h2 class="text-3xl font-bold text-white text-center mb-16">Features that bring your characters to life</h2>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-12">
          <!-- Character Creation -->
          <div class="bg-gray-900 rounded-xl p-8">
            <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-6">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-white mb-4">Personalized Characters</h3>
            <p class="text-gray-400">Create unique AI characters with distinct personalities, voices, and appearances. Make them public or keep them private.</p>
          </div>

          <!-- AI Chat -->
          <div class="bg-gray-900 rounded-xl p-8">
            <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-6">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-white mb-4">Natural Conversations</h3>
            <p class="text-gray-400">Engage in dynamic, context-aware conversations powered by advanced AI technology.</p>
          </div>

          <!-- Voice Generation -->
          <div class="bg-gray-900 rounded-xl p-8">
            <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-6">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-white mb-4">Voice Synthesis</h3>
            <p class="text-gray-400">Give your characters a voice with multiple voice options for more immersive interactions.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="relative max-w-7xl mx-auto px-8 py-24">
      <div class="bg-indigo-600 rounded-2xl p-12 text-center">
        <h2 class="text-3xl font-bold text-white mb-6">Ready to bring your characters to life?</h2>
        <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
          Join CharactersHub today and start creating AI characters that feel real, think independently, and engage in meaningful conversations.
        </p>
        <Link :href="route('register')" class="inline-block bg-white text-indigo-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-indigo-50 transition">
          Get Started for Free
        </Link>
      </div>
    </div>

    <!-- Footer -->
    <footer class="relative bg-gray-900">
      <div class="max-w-7xl mx-auto px-8 py-12">
        <div class="flex justify-between items-center">
          <div class="text-xl font-bold text-white">CharactersHub</div>
          <div class="text-gray-400">&copy; {{ new Date().getFullYear() }} CharactersHub. All rights reserved.</div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<style>
html {
  scroll-behavior: smooth;
}
</style>
