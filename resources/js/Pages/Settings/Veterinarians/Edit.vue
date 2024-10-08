<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsAside from '../Partials/SettingsAside.vue';
import VeterinarianForm from './Partials/VeterinarianForm.vue';
import DeleteVeterinarianForm from './Partials/DeleteVeterinarianForm.vue';
import {__} from '@/Composables/Translate';

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
  user_id: props.veterinarian.user?.id,
  name: props.veterinarian.name,
  address: props.veterinarian.address,
  city: props.veterinarian.city,
  subdivision: props.veterinarian.subdivision,
  postal_code: props.veterinarian.postal_code,
  phone: props.veterinarian.phone,
  email: props.veterinarian.email,
  license: props.veterinarian.license,
  business_name: props.veterinarian.business_name,
});

const updateVeterinarian = () => {
  form.put(route('veterinarians.update', props.veterinarian), {
    preserveScroll: true
  });
};
</script>

<template>
  <AppLayout title="Veterinarians">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <VeterinarianForm
          :form="form"
          :title="__('Update Veterinarian')"
          :action="__('Update Veterinarian')"
          :users="users"
          :veterinarian="veterinarian"
          @action-clicked="updateVeterinarian"
        />
        <DeleteVeterinarianForm
          :veterinarian="veterinarian"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>
