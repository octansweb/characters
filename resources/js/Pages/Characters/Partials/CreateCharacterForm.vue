<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue'; // Assuming you have this component
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    description: '',
    personality: '',
    is_public: true,
    gender: '',  // Added gender field
    avatar: null,  // Added avatar file
    voice: '',
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Create a New Character
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Fill in the details to create your new character.
            </p>
        </header>

        <form @submit.prevent="form.post(route('characters.store'), { forceFormData: true })" class="mt-6 space-y-6">
            <!-- Name Field -->
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Avatar File Upload Field -->
            <div>
                <InputLabel for="avatar" value="Avatar" />

                <input
                    id="avatar"
                    type="file"
                    class="mt-1 block w-full"
                    @change="(e) => form.avatar = e.target.files[0]"
                />

                <InputError class="mt-2" :message="form.errors.avatar" />
            </div>

            <!-- Description Field -->
            <div>
                <InputLabel for="description" value="Description" />

                <textarea
                    id="description"
                    v-model="form.description"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                ></textarea>

                <InputError class="mt-2" :message="form.errors.description" />
            </div>

            <!-- Personality Field -->
            <div>
                <InputLabel for="personality" value="Personality" />

                <textarea
                    id="personality"
                    v-model="form.personality"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                ></textarea>

                <InputError class="mt-2" :message="form.errors.personality" />
            </div>

            <!-- Gender Field -->
            <div>
                <InputLabel for="gender" value="Gender" />

                <select
                    id="gender"
                    v-model="form.gender"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required
                >
                    <option value="" disabled>Please select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>

                <InputError class="mt-2" :message="form.errors.gender" />
            </div>

            <!-- Voice Field -->
            <div>
                <InputLabel for="voice" value="Voice" />

                <select
                    id="voice"
                    v-model="form.voice"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required
                >
                    <option value="" disabled>Please select</option>
                    <option value="Matthew">Matthew - Male - en-US</option>
                    <option value="Stephen">Stephen - Male - en-US</option>
                    <option value="Olivia">Olivia - Female - en-AU</option>
                    <option value="Amy">Amy - Female - en-GB</option>
                    <option value="Danielle">Danielle - Female - en-US</option>
                    <option value="Joanna">Joanna - Female - en-US</option>
                    <option value="Ruth">Ruth - Female - en-US</option>
                </select>

                <InputError class="mt-2" :message="form.errors.voice" />
            </div> 

            <!-- Is Public Field -->
            <div class="flex items-center">
                <Checkbox
                    id="is_public"
                    v-model:checked="form.is_public"
                />

                <label for="is_public" class="ml-2 text-sm text-gray-600">
                    Make this character public
                </label>
            </div>

            <InputError class="mt-2" :message="form.errors.is_public" />

            <!-- Submit Button -->
            <div class="flex items-center gap-4 mt-4">
                <PrimaryButton :disabled="form.processing">Create Character</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out duration-500"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out duration-500"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
                        Character created successfully.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
