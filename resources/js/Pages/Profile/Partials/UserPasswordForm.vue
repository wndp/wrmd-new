<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
});

const updatePassword = () => {
  form.put(route('profile.update.password'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
    onError: () => {
        if (form.errors.password) {
            form.reset('password', 'password_confirmation');
            passwordInput.value.focus();
        }
        if (form.errors.current_password) {
            form.reset('current_password');
            currentPasswordInput.value.focus();
        }
    },
  });
};
</script>

<template>
  <FormSection>
    <div class="col-span-4 sm:col-span-3">
      <InputLabel for="current_password">
        {{ __('Current Password') }}
      </InputLabel>
      <TextInput
        ref="currentPasswordInput"
        v-model="form.current_password"
        name="current_password"
        type="password"
        autocomplete="current-password"
        class="mt-1"
      />
      <InputError
        :message="form.errors.current_password"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-3">
      <InputLabel for="password">
        {{ __('New Password') }}
      </InputLabel>
      <TextInput
        ref="passwordInput"
        v-model="form.password"
        name="password"
        type="password"
        autocomplete="new-password"
        class="mt-1"
      />
      <InputError
        :message="form.errors.password"
        class="mt-2"
      />
    </div>
    <div class="col-span-4 sm:col-span-3">
      <InputLabel for="password_confirmation">
        {{ __('Confirm Password') }}
      </InputLabel>
      <TextInput
        v-model="form.password_confirmation"
        name="password_confirmation"
        type="password"
        autocomplete="new-password"
        class="mt-1"
      />
      <InputError
        :message="form.errors.password_confirmation"
        class="mt-2"
      />
    </div>
    <template #actions>
      <ActionMessage
        :on="form.recentlySuccessful"
        class="mr-3"
      >
        {{ __('Saved.') }}
      </ActionMessage>
      <PrimaryButton
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="updatePassword"
      >
        {{ __('Update Password') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
