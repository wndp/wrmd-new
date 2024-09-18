<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import {__} from '@/Composables/Translate';

const password = ref(null);
const confirmingUserDeletion = ref(false);
const showConfirm = ref(false);

const form = useForm({
    password: '',
    password_confirmation: ''
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    setTimeout(() => password.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('profile.current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => password.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};
</script>

<template>
  <div>
    <FormSection>
      <template #title>
        {{ __('Delete User Account') }}
      </template>
      <template #description>
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
      </template>

      <template #actions>
        <DangerButton @click="confirmUserDeletion">
          {{ __('Delete User Account') }}
        </DangerButton>
      </template>
    </FormSection>

    <ConfirmationModal
      :show="confirmingUserDeletion"
      @close="closeModal"
    >
      <template #title>
        {{ __('Delete User Account') }}
      </template>

      <template #content>
        <p class="text-gray-700">
          {{ __('Are you sure you want to delete your user account? Once your user account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your user account.') }}
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
          @click="deleteUser"
        >
          {{ __('Delete User Account') }}
        </DangerButton>
      </template>
    </ConfirmationModal>
  </div>
</template>
