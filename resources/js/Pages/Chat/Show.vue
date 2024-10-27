<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref, onMounted, nextTick } from 'vue';

const props = defineProps({
    character: {
        required: true,
        type: Object,
    },
    conversation: {
        required: true,
        type: Array,
    },
});

// Reactive references
const message = ref('');
// Exclude system messages from the displayed conversation
const messages = ref([...props.conversation.filter(msg => msg.role !== 'system')]);

// Auto-scroll to the bottom
const messagesContainer = ref(null);
const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

// Watch for new messages
onMounted(() => {
    scrollToBottom();
});

const sendMessage = async () => {
    if (message.value.trim() === '') return;

    // Append the user's message to the messages array
    messages.value.push({
        role: 'user',
        content: message.value,
    });

    const userMessage = message.value;
    message.value = '';
    scrollToBottom();

    try {
        const response = await fetch(route('characters.stream', props.character.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'text/plain',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                message: userMessage,
            }),
        });

        if (!response.ok) {
            console.error('Network response was not ok');
            return;
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder('utf-8');

        // Push a new assistant message into messages
        messages.value.push({
            role: 'assistant',
            content: '',
        });

        // Get a reference to the last message in messages
        const assistantMessage = messages.value[messages.value.length - 1];
        scrollToBottom();

        let done = false;

        while (!done) {
            const { value, done: readerDone } = await reader.read();
            done = readerDone;

            if (value) {
                const chunk = decoder.decode(value);
                assistantMessage.content += chunk;
                scrollToBottom();
            }
        }
    } catch (error) {
        console.error('Fetch error:', error);
    }
};

let playingMessage = ref(null);
let audio = null;

let playMessage = (message) => {
    if (audio) {
        audio.pause();
        audio.currentTime = 0;
    }

    audio = new Audio(message.speech_file_path);
    playingMessage.value = message.id;

    // Play the audio file
    audio.play().then(() => {
        console.log('Playing audio:', message.speech_file_path);
    }).catch(error => {
        console.error('Error playing audio:', error);
    });

    // Reset playingMessage.value to null when the audio finishes playing
    audio.addEventListener('ended', () => {
        playingMessage.value = null;
        console.log('Audio playback finished.');
    });
};


</script>

<template>

    <Head :title="`Chat with ${character.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Chat with {{ character.name }}
            </h2>
        </template>

        <div>
            <div class="flex justify-between items-center border-b border-gray-100  p-4">
                <div class="flex items-center">
                    <img :src="character.avatar_url || 'https://via.placeholder.com/40'" alt="Assistant Avatar"
                        class="w-10 h-10 rounded-full mr-2" />

                    <div>
                        <div class="text-lg font-medium" v-text="character.name"></div>
                        <div class="text-xs" v-text="'By: ' + character.user.name"></div>
                    </div>
                </div>

                <div>
                    <PrimaryButton>Clear Chat</PrimaryButton>
                </div>
            </div>
            <div class="bg-white">
                <!-- Adjusted the height to fill available space -->
                <!-- <div class="flex flex-col h-[calc(100vh-16rem)]"> -->
                <div class="flex flex-col h-[90vh]">
                    <!-- Chat Messages -->
                    <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-4">
                        <div v-for="(msg, index) in messages" :key="index" class="flex" :class="{
                            'justify-end': msg.role === 'user',
                            'justify-start': msg.role !== 'user',
                        }">
                            <!-- Avatar for assistant -->
                            <div v-if="msg.role === 'assistant'" class="">
                                <img :src="character.avatar_url || 'https://via.placeholder.com/40'"
                                    alt="Assistant Avatar" class="w-10 h-10 rounded-full mr-2" />



                                
                                <div class="flex justify-center mt-2 mr-2">
                                    <!-- Play icon -->
                                    <svg v-if="playingMessage !== msg.id" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        @click.prevent="playMessage(msg)"
                                        class="size-6 cursor-pointer">
                                        <path fill-rule="evenodd"
                                            d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <!-- Play icon -->

                                </div>
                            </div>

                            <!-- Message Bubble -->
                            <div class="max-w-md px-4 py-2 rounded-lg" :class="{
                                'bg-blue-500 text-white': msg.role === 'user',
                                'bg-white text-gray-800 border': msg.role !== 'user',
                            }">
                                <p class="whitespace-pre-wrap">{{ msg.content }}</p>
                            </div>

                            <!-- Spacer for user's messages -->
                            <div v-if="msg.role === 'user'" class="ml-2"></div>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="border-t p-4">
                        <form @submit.prevent="sendMessage" class="flex">
                            <textarea v-model="message" rows="1" placeholder="Type your message..."
                                class="flex-1 resize-none border border-gray-300 rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            <button type="submit"
                                class="bg-blue-500 text-white px-4 py-2 rounded-r-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Send
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Optional: Customize the scrollbar */
.messagesContainer::-webkit-scrollbar {
    width: 8px;
}

.messagesContainer::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 4px;
}
</style>
