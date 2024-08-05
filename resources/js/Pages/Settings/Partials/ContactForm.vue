<script setup>
import {useForm, usePage} from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const form = useForm({
  contact_name: usePage().props.auth.user.current_team.contact_name,
  contact_email: usePage().props.auth.user.current_team.contact_email,
  phone_number: usePage().props.auth.user.current_team.phone_number,
  website: usePage().props.auth.user.current_team.website,
});

const updateProfile = () => {
  form.put(route('account.profile.update.contact'), {
    preserveScroll: true
  });
}
</script>

<template>
  <FormSection>
    <template #title>
      {{ __('Contact Details') }}
    </template>
    <template #description>
      {{ __('Update your contact information.') }}
    </template>

    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="first-name">
        {{ __('Contact name') }}
      </InputLabel>
      <TextInput
        v-model="form.contact_name"
        name="contact_name"
        autocomplete="given-name"
        class="mt-1"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="email-address">
        {{ __('Email address') }}
      </InputLabel>
      <TextInput
        v-model="form.contact_email"
        type="email"
        name="contact_email"
        autocomplete="email"
        class="mt-1"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="phone-number">
        {{ __('Phone Number') }}
      </InputLabel>
      <TextInput
        v-model="form.phone_number"
        name="phone_number"
        autocomplete="tel"
        class="mt-1"
      />
    </div>
    <div class="col-span-4 sm:col-span-2">
      <InputLabel for="website">
        {{ __('Website') }}
      </InputLabel>
      <TextInput
        v-model="form.website"
        type="url"
        name="website"
        autocomplete="url"
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
        {{ __('Update Contact Info') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
