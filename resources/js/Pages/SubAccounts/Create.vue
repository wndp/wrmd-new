<script setup>
import {computed} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import ProfileForm from './Partials/ProfileForm.vue';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  users: {
    type: Array,
    required: true
  }
});

const form = useForm({
  name: '',
  federal_permit_number: '',
  subdivision_permit_number: '',
  country: usePage().props.auth.team.country,
  address: '',
  city: '',
  subdivision: usePage().props.auth.team.subdivision,
  postal_code: '',
  contact_name: '',
  contact_email: '',
  phone_number: '',
  notes: '',
  clone_settings: true,
  add_current_user: true,
  clone_extensions: true,
  clone_custom_fields: true,
  users: []
});

const masterAccount = computed(() => usePage().props.auth.team);

const store = () => {
  form.post(route('sub_accounts.store'));
};

const copyFromMasterAccount = () => {
  form.country = masterAccount.value.country;
  form.address = masterAccount.value.address;
  form.city = masterAccount.value.city;
  form.subdivision = masterAccount.value.subdivision;
  form.postal_code = masterAccount.value.postal_code;
  form.contact_name = masterAccount.value.contact_name;
  form.contact_email = masterAccount.value.contact_email;
  form.phone_number = masterAccount.value.phone_number;
}
</script>

<template>
  <AppLayout title="Sub-Accounts">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ __('New Sub-Account') }}
      </h1>
    </template>
    <Link
      :href="route('sub_accounts.index')"
      class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
    >
      <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
      {{ __('Return to Sub-Accounts List') }}
    </Link>
    <div class="mt-8">
      <SecondaryButton @click="copyFromMasterAccount">
        {{ __('Copy Details from Master Account') }}
      </SecondaryButton>
    </div>
    <ProfileForm
      :form="form"
      :canSubmit="false"
      class="mt-2"
    />
    <Panel class="mt-8">
      <template #title>
        {{ __('Sub-Account Setup') }}
      </template>
      <template #content>
        <FormRow
          id="clone_settings"
          class="col-span-6"
        >
          <Toggle
            v-model="form.clone_settings"
            :label="__('Copy settings from :organization into sub-account.', {organization: masterAccount.name})"
            dusk="clone_settings"
          />
          <InputError
            :message="form.errors.clone_settings"
            class="mt-1"
          />
        </FormRow>
        <FormRow
          id="clone_extensions"
          class="col-span-6"
        >
          <Toggle
            v-model="form.clone_extensions"
            :label="__('Copy activated extensions from :organization into sub-account.', {organization: masterAccount.name})"
            dusk="clone_extensions"
          />
          <InputError
            :message="form.errors.clone_extensions"
            class="mt-1"
          />
        </FormRow>
        <FormRow
          id="clone_custom_fields"
          class="col-span-6"
        >
          <Toggle
            v-model="form.clone_custom_fields"
            :label="__('Copy custom fields from :organization into sub-account.', {organization: masterAccount.name})"
            dusk="clone_custom_fields"
          />
          <InputError
            :message="form.errors.clone_custom_fields"
            class="mt-1"
          />
        </FormRow>
        <FormRow
          id="add_current_user"
          class="col-span-6"
        >
          <Toggle
            v-model="form.add_current_user"
            :label="__('Add current user (:email) to sub-account.', {email: usePage().props.auth.user.email})"
            dusk="add_current_user"
          />
          <InputError
            :message="form.errors.add_current_user"
            class="mt-1"
          />
        </FormRow>
      </template>
    </Panel>
    <Panel class="mt-8">
      <template #title>
        {{ __('Add These Additional Users') }}
      </template>
      <template #content>
        <FormRow
          id="add_current_user"
          class="col-span-6"
        >
          <InputError
            :message="form.errors.users"
            class="mt-1"
          />
          <div
            v-for="loopUser in users"
            :key="loopUser.id"
            class="flex items-start"
          >
            <div class="flex items-center">
              <Checkbox
                :id="loopUser.email"
                v-model="form.users"
                :value="loopUser.id"
              />
              <InputLabel
                :for="loopUser.email"
                class="ml-2"
              >
                {{ loopUser.name }} <span class="text-gray-500">&mdash; {{ loopUser.email }}</span>
              </InputLabel>
            </div>
          </div>
        </FormRow>
      </template>
    </Panel>
    <PrimaryButton
      class="mt-8"
      :class="{ 'opacity-25': form.processing }"
      :disabled="form.processing"
      @click="store"
    >
      {{ __('Create New Sub-Account') }}
    </PrimaryButton>
  </AppLayout>
</template>
