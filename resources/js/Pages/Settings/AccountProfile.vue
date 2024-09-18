<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsAside from './Partials/SettingsAside.vue';
import AccountProfileForm from './Partials/AccountProfileForm.vue';
import ContactForm from './Partials/ContactForm.vue';
import LocalizationForm from './Partials/LocalizationForm.vue';

const mutableTimezones = ref(usePage().props.options.timezoneOptions)

const onCountryChange = (locale) => {
  mutableTimezones.value = locale.timezones;
};
</script>

<template>
  <AppLayout title="Profile">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <AccountProfileForm
          class="mt-8"
          @country-change="onCountryChange"
        />
        <ContactForm class="mt-8" />
        <LocalizationForm
          :timezones="mutableTimezones"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>
