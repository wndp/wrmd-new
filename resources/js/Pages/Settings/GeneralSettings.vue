<template>
  <AppLayout title="General Settings">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <GenericSettingsForm
          :general-settings="generalSettings"
          :selectable-fields="selectableFields"
          :listed-fields="listedFields"
          class="mt-8"
        />
        <TreatmentLogSettingsForm
          :general-settings="generalSettings"
          class="mt-8"
        />
        <LocationsSettingsForm
          :general-settings="generalSettings"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsAside from './Partials/SettingsAside.vue';
import GenericSettingsForm from './Partials/GenericSettingsForm.vue';
import TreatmentLogSettingsForm from './Partials/TreatmentLogSettingsForm.vue';
import LocationsSettingsForm from './Partials/LocationsSettingsForm.vue';

defineProps({
  generalSettings: Object,
  selectableFields: Array,
  listedFields: Array
});
</script>
