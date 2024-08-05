<script setup>
import {computed} from 'vue';
import {useForm} from '@inertiajs/vue3';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  status: String,
})

const form = useForm({});
const verificationLinkSent = computed(() => props.status === 'verification-link-sent');

const submit = () => {
    form.post(route('verification.send'))
};
</script>

<template>
  <PublicLayout title="Email Verification">
    <div class="flex justify-center">
      <div class="w-full sm:max-w-lg mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-4 text-sm text-gray-600">
          {{ __("Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.") }}
        </div>
        <div
          v-if="verificationLinkSent"
          class="mb-4 font-medium text-sm text-green-600"
        >
          {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
        <form @submit.prevent="submit">
          <div class="mt-4 flex items-center justify-between">
            <PrimaryButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              type="submit"
            >
              {{ __('Resend Verification Email') }}
            </PrimaryButton>
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="underline text-sm text-gray-600 hover:text-gray-900"
            >
              {{ __('Sign Out') }}
            </Link>
          </div>
        </form>
      </div>
    </div>
  </PublicLayout>
</template>
