<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsAside from '../Partials/SettingsAside.vue';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import Alert from '@/Components/Alert.vue';
import isEmpty from 'lodash/isEmpty';
import { useForm, router } from '@inertiajs/vue3';
import { ref, inject } from 'vue';
import {__} from '@/Composables/Translate';

const route = inject('route');

let form = useForm({
    name: '',
    email: '',
    email_confirmation: '',
    role: 'User',
    password: '',
    password_confirmation: '',
    send_email: false
});

let emailExists = ref(false);
let role = ref(null);

const storeUser = () => {
    form.post(route('users.store'), {
        errorBag: 'addTeamMember',
        preserveScroll: true
    });
};

const lookUpUser = () => {
  window.axios.get('/internal-api/users/search/?email=' + form.email)
    .then(response => {
      emailExists.value = ! isEmpty(response.data);
      role.value.focus();
    });
};

const goBack = () => {
    router.get(route('users.index'));
}
</script>

<template>
  <AppLayout title="Users">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <FormSection>
          <template #title>
            {{ __('New User') }}
          </template>
          <template #description>
            {{ __('Use this form to add a new user to your account. Users can exist in multiple accounts and may already be known to Wildlife Rehabilitation MD.') }}
          </template>
          <div class="col-span-4 sm:col-span-2">
            <InputLabel for="email">{{ __('Email address') }}</InputLabel>
            <TextInput
              v-model="form.email"
              type="email"
              name="email"
              autocomplete="email"
              class="mt-1"
              @change="lookUpUser"
            />
            <InputError
              :message="form.errors.email"
              class="mt-2"
            />
          </div>
          <div
            v-if="!emailExists"
            class="col-span-4 sm:col-span-2"
          >
            <InputLabel for="email_confirmation">{{ __('Email address confirmation') }}</InputLabel>
            <TextInput
              v-model="form.email_confirmation"
              type="email"
              name="email_confirmation"
              autocomplete="off"
              class="mt-1"
            />
          </div>
          <div
            v-if="!emailExists"
            class="col-span-4 sm:col-span-2"
          >
            <InputLabel for="name">{{ __('Name') }}</InputLabel>
            <TextInput
              v-model="form.name"
              name="name"
              autocomplete="given-name"
              class="mt-1"
            />
            <InputError
              :message="form.errors.name"
              class="mt-2"
            />
          </div>
          <div class="col-span-4 sm:col-span-2">
            <InputLabel for="role">{{ __('Role') }}</InputLabel>
            <SelectInput
              ref="role"
              v-model="form.role"
              name="role"
              :options="$page.props.options.roles"
              class="mt-1"
            />
            <InputError
              :message="form.errors.role"
              class="mt-2"
            />
          </div>
          <div
            v-if="!emailExists"
            class="col-span-4 sm:col-span-2"
          >
            <InputLabel for="password">{{ __('Password') }}</InputLabel>
            <TextInput
              v-model="form.password"
              type="password"
              name="password"
              autocomplete="off"
              class="mt-1"
            />
            <InputError
              :message="form.errors.password"
              class="mt-2"
            />
          </div>
          <div
            v-if="!emailExists"
            class="col-span-4 sm:col-span-2"
          >
            <InputLabel for="password_confirmation">{{ __('Password Confirmation') }}</InputLabel>
            <TextInput
              v-model="form.password_confirmation"
              type="password"
              name="password_confirmation"
              autocomplete="off"
              class="mt-1"
            />
          </div>
          <div
            v-if="!emailExists"
            class="col-span-4"
          >
            <div class="relative flex items-start mr-8">
              <div class="flex items-center h-5">
                <Checkbox
                  id="send_email"
                  v-model="form.send_email"
                  name="send_email"
                />
              </div>
              <div class="ml-3 text-sm">
                <InputLabel for="send_email">{{ __('Send a welcome email, including the password, to the new user.') }}</InputLabel>
              </div>
            </div>
          </div>
          <Alert
            v-show="emailExists"
            class="col-span-4"
          >
            {{ __('This email belongs to a user of a different account. You may add them to your account, however you will not be able to change their name or password.') }}
          </Alert>
          <template #actions>
            <SecondaryButton @click="goBack">
              {{ __('Nevermind') }}
            </SecondaryButton>
            <ActionMessage
              :on="form.recentlySuccessful"
              class="mr-3"
            >
              {{ __('Saved.') }}
            </ActionMessage>
            <PrimaryButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="storeUser"
            >
              {{ __('Create New User') }}
            </PrimaryButton>
          </template>
        </FormSection>
      </div>
    </div>
  </AppLayout>
</template>
