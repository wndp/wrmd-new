<script setup>
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import {ArrowLongLeftIcon} from '@heroicons/vue/24/outline';
import SubAccountTabs from './Partials/SubAccountTabs.vue';
import ProfileForm from './Partials/ProfileForm.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  subAccount: {
    type: Object,
    required: true
  },
});

const form = useForm({
  name: props.subAccount.name,
  federal_permit_number: props.subAccount.federal_permit_number,
  subdivision_permit_number: props.subAccount.subdivision_permit_number,
  country: props.subAccount.country,
  address: props.subAccount.address,
  city: props.subAccount.city,
  subdivision: props.subAccount.subdivision,
  postal_code: props.subAccount.postal_code,
  contact_name: props.subAccount.contact_name,
  contact_email: props.subAccount.contact_email,
  phone_number: props.subAccount.phone_number,
  notes: props.subAccount.notes,
});

const updateProfile = () => {
  form.put(route('sub_accounts.update', props.subAccount), {
    preserveScroll: true,
  });
}
</script>

<template>
  <AppLayout title="Sub-Accounts">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ subAccount.name }}
      </h1>
    </template>
    <Link
      :href="route('sub_accounts.index')"
      class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
    >
      <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
      {{ __('Return to Sub-Accounts List') }}
    </Link>
    <SubAccountTabs
      :subAccount="subAccount"
      class="mt-4"
    />
    <ProfileForm
      :form="form"
      class="mt-8"
      @submitted="updateProfile"
    />
  </AppLayout>
</template>
