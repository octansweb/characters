<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue'; // Assuming you have this component
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    avatar_url: '',
    description: '',
    personality: '',
    is_public: true,
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

        <form @submit.prevent="form.post(route('characters.store'))" class="mt-6 space-y-6">
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

            <!-- Avatar URL Field -->
            <div>
                <InputLabel for="avatar_url" value="Avatar URL" />

                <TextInput
                    id="avatar_url"
                    type="url"
                    class="mt-1 block w-full"
                    v-model="form.avatar_url"
                />

                <InputError class="mt-2" :message="form.errors.avatar_url" />
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
