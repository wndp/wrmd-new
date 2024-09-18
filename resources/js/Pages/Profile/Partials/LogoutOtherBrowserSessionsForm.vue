<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import {__} from '@/Composables/Translate';

const password = ref(null);
const confirmingLogout = ref(false);
const showConfirm = ref(false);

const form = useForm({
    password: '',
    password_confirmation: ''
});

const confirmLogout = () => {
    confirmingLogout.value = true;
    setTimeout(() => password.value.focus(), 250);
};

const logoutOtherBrowserSessions = () => {
    form.delete(route('profile.other-browser-sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => password.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingLogout.value = false;
    form.reset();
};
</script>

<template>
  <div>
    <FormSection>
      <template #title>
        {{ __('Browser Sessions') }}
      </template>
      <template #description>
        {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices. If you feel your user account has been compromised, you should also update your password.') }}
      </template>

      <template #actions>
        <PrimaryButton @click="confirmLogout">
          {{ __('Log Out Other Browser Sessions') }}
        </PrimaryButton>
      </template>
    </FormSection>

    <ConfirmationModal
      :show="confirmingLogout"
      @close="closeModal"
    >
      <template #title>
        {{ __('Log Out Other Browser Sessions') }}
      </template>

      <template #content>
        <p class="text-gray-700">
          {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}
        </p>
        <div class="mt-4">
          <InputLabel for="password">
            {{ __('Your password') }}
          </InputLabel>
          <TextInput
            ref="password"
            v-model="form.password"
            type="password"
            name="password"
            autocomplete="off"
            class="mt-1"
            @blur="showConfirm = true"
          />
          <InputError
            :message="form.errors.password"
            class="mt-2"
          />
        </div>
        <div
          v-if="showConfirm && form.password.length > 1"
          class="mt-4"
        >
          <InputLabel for="password_confirmation">
            {{ __('Confirm Your password') }}
          </InputLabel>
          <TextInput
            v-model="form.password_confirmation"
            type="password"
            name="password_confirmation"
            autocomplete="off"
            class="mt-1"
          />
        </div>
      </template>

      <template #footer>
        <SecondaryButton
          class="mr-3"
          @click="closeModal"
        >
          {{ __('Nevermind') }}
        </SecondaryButton>

        <DangerButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="logoutOtherBrowserSessions"
        >
          {{ __('Log Out Other Browser Sessions') }}
        </DangerButton>
      </template>
    </ConfirmationModal>
  </div>
</template>
