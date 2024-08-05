<script setup>
import {useForm} from '@inertiajs/vue3';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import {__} from '@/Composables/Translate';

defineProps({
  status: {
      type: String,
      default: ''
  }
});

const form = useForm({
    email: ''
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
  <PublicLayout title="Forgot Password">
    <div class="flex justify-center">
      <div class="w-full sm:max-w-lg mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-4 text-sm text-gray-600">
          {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>
        <div
          v-if="status"
          class="mb-4 font-medium text-sm text-green-600"
        >
          {{ status }}
        </div>
        <form @submit.prevent="submit">
          <div>
            <InputLabel for="email">
              {{ __('Email address') }}
            </InputLabel>
            <TextInput
              v-model="form.email"
              name="email"
              type="email"
              class="mt-1 block w-full"
              required
              autofocus
              autocomplete="email"
            />
            <InputError
              :message="form.errors.email"
              class="mt-1"
            />
          </div>
          <div class="flex items-center justify-end mt-4">
            <PrimaryButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              type="submit"
            >
              {{ __('Email Password Reset Link') }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </PublicLayout>
</template>
