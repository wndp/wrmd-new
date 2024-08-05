<template>
  <AppLayout title="Veterinarians">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <VeterinarianForm
          v-model="form.veterinarian"
          :title="__('New Veterinarian')"
          :action="__('Create New Veterinarian')"
          :users="users"
          :can-submit="false"
          :errors="form.errors"
          @saved="storeVeterinarian"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsAside from '../Partials/SettingsAside.vue';
import VeterinarianForm from './Partials/VeterinarianForm.vue';

const route = inject('route');

defineProps({
  users: {
    type: Array,
    required: true
  }
});

const form = useForm({
    veterinarian: {}
});

const storeVeterinarian = () => {
  form.transform(data => {
    return data.veterinarian;
  })
  .post(route('veterinarians.store'), {
    preserveScroll: true
  });
};
</script>
