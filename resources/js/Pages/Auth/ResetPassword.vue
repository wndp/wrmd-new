<script setup>
import {useForm} from '@inertiajs/vue3';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  email: String,
  token: String,
});

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
})

const submit = () => form.post(route('password.update'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
});
</script>

<template>
  <PublicLayout title="Reset Password">
    <div class="flex justify-center">
      <div class="w-full sm:max-w-lg mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-4 text-sm text-gray-600">
          {{ __('Reset your password with your email address and a strong new password.') }}
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
          <div class="mt-4">
            <InputLabel for="password">
              {{ __('Password') }}
            </InputLabel>
            <TextInput
              v-model="form.password"
              name="password"
              type="password"
              class="mt-1 block w-full"
              required
              autocomplete="new-password"
            />
            <InputError
              :message="form.errors.password"
              class="mt-1"
            />
          </div>
          <div class="mt-4">
            <InputLabel for="password_confirmation">
              {{ __('Confirm Password') }}
            </InputLabel>
            <TextInput
              v-model="form.password_confirmation"
              name="password_confirmation"
              type="password"
              class="mt-1 block w-full"
              required
              autocomplete="new-password"
            />
          </div>
          <div class="flex items-center justify-end mt-4">
            <PrimaryButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              type="submit"
            >
              {{ __('Reset Password') }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </PublicLayout>
</template>
