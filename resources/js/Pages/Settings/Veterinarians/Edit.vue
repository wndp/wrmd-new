<template>
  <AppLayout title="Veterinarians">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <VeterinarianForm
          v-model="form.veterinarian"
          :title="__('Update Veterinarian')"
          :action="__('Update Veterinarian')"
          :users="users"
          :veterinarian="veterinarian"
          :can-submit="false"
          :errors="form.errors"
          @saved="updateVeterinarian"
        />
        <DeleteVeterinarianForm
          :veterinarian="veterinarian"
          class="mt-8"
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
import DeleteVeterinarianForm from './Partials/DeleteVeterinarianForm.vue';

const route = inject('route');

const props = defineProps({
  veterinarian: {
    type: Object,
    required: true
  },
  users: {
    type: Array,
    required: true
  }
});

const form = useForm({
  veterinarian: {}
});

const updateVeterinarian = () => {
  form.transform(data => {
    return data.veterinarian;
  }).put(route('veterinarians.update', props.veterinarian), {
    preserveScroll: true
  });
};
</script>
