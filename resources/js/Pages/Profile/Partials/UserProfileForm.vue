<script setup>
import {useForm, usePage} from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import Alert from '@/Components/Alert.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const form = useForm({
  name: usePage().props.auth.user.name,
  email: usePage().props.auth.user.email,
});

const updateProfile = () => {
  form.put(route('profile.update'), {
    preserveScroll: true
  });
};
</script>

<template>
  <FormSection>
    <Alert class="col-span-4">
      {{ __('Your role and permissions can only be changed in the settings section by a Super Admin user.') }}
    </Alert>
    <div class="col-span-4 sm:col-span-3">
      <InputLabel for="name">
        {{ __('Name') }}
      </InputLabel>
      <TextInput
        v-model="form.name"
        name="name"
        autocomplete="given-name"
        class="mt-1"
      />
    </div>
    <div class="col-span-4 sm:col-span-3">
      <InputLabel for="email">
        {{ __('Email address') }}
      </InputLabel>
      <TextInput
        v-model="form.email"
        type="email"
        name="email"
        autocomplete="email"
        class="mt-1"
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
        @click="updateProfile"
      >
        {{ __('Update Profile') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
