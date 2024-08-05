<script setup>
import {useForm} from '@inertiajs/vue3';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  canResetPassword: Boolean,
  status: String,
  honeypot: {
    type: Object,
    required: true
  },
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
  [props.honeypot.nameFieldName]: '',
  [props.honeypot.validFromFieldName]: props.honeypot.encryptedValidFrom,
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
  <PublicLayout title="Log in">
    <div class="flex justify-center">
      <div class="w-full sm:max-w-lg mt-6 mb-12 shadow-md overflow-hidden sm:rounded-lg">
        <div class="px-6 py-4 bg-white">
          <div
            v-if="status"
            class="mb-4 font-medium text-sm text-green-600"
          >
            {{ status }}
          </div>
          <form @submit.prevent="submit">
            <div>
              <InputLabel for="email">{{ __('Email address') }}</InputLabel>
              <TextInput
                v-model="form.email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                required
                autofocus
                autocomplete="email"
              />
              <InputError :message="form.errors.email" />
            </div>
            <div class="mt-4">
              <InputLabel for="password">{{ __('Password') }}</InputLabel>
              <TextInput
                v-model="form.password"
                name="password"
                type="password"
                class="mt-1 block w-full"
                required
                autocomplete="current-password"
              />
              <InputError :message="form.errors.password" />
            </div>
            <div class="block mt-4">
              <label class="flex items-center">
                <Checkbox
                  v-model="form.remember"
                  name="remember"
                />
                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
              </label>
            </div>
            <div
              v-if="honeypot.enabled"
              :name="`${honeypot.nameFieldName}_wrap`"
              style="display:none;"
            >
              <input
                :id="honeypot.nameFieldName"
                v-model="form[honeypot.nameFieldName]"
                type="text"
                :name="honeypot.nameFieldName"
              >
              <input
                v-model="form[honeypot.validFromFieldName]"
                type="text"
                :name="honeypot.validFromFieldName"
              >
            </div>
            <div class="flex items-center justify-end mt-4">
              <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="underline text-sm text-gray-600 hover:text-gray-900"
              >
                {{ __('Forgot your password?') }}
              </Link>
              <PrimaryButton
                class="ml-4"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="submit"
              >
                {{ __('Sign in') }}
              </PrimaryButton>
            </div>
          </form>
        </div>
        <div class="px-4 py-6 bg-gray-50 border-t-2 border-gray-200 sm:px-10">
          <h4 class="text-sm font-bold text-gray-500 tracking-wider uppercase">
            {{ __('Need An Account?') }}
          </h4>
          <p class="text-sm font-normal text-gray-500 mt-2">
            {{ __('Before signing into Wildlife Rehabilitation MD, your organization first needs to create a free account.') }}
          </p>
          <Link
            :href="route('register')"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-300 disabled:bg-green-400 transition mt-4"
          >
            {{ __('Register Your Organization') }}
          </Link>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>
