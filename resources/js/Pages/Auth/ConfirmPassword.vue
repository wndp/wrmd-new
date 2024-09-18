<script setup>
import {useForm} from '@inertiajs/vue3';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import InputError from '@/Components/FormElements/InputError.vue';

const form = useForm({
  password: '',
});

const submit = () => {
  form.post(route('password.confirm'), {
    onFinish: () => form.reset(),
  });
}
</script>

<template>
  <PublicLayout title="Confirm Password">
    <div class="flex justify-center">
      <div class="w-full sm:max-w-lg mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-4 text-sm text-gray-600">
          {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>
        <form @submit.prevent="submit">
          <div>
            <InputLabel for="password">
              {{ __('Password') }}
            </InputLabel>
            <TextInput
              id="password"
              v-model="form.password"
              type="password"
              class="mt-1 block w-full"
              required
              autocomplete="current-password"
              autofocus
            />
            <InputError
              :message="form.password.email"
              class="mt-1"
            />
          </div>
          <div class="flex justify-end mt-4">
            <PrimaryButton
              class="ml-4"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              {{ __('Confirm') }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </PublicLayout>
</template>
