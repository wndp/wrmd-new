<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsAside from '../Partials/SettingsAside.vue';
import VeterinarianForm from './Partials/VeterinarianForm.vue';
import {__} from '@/Composables/Translate';

const route = inject('route');

defineProps({
  users: {
    type: Array,
    required: true
  }
});

const form = useForm({
    name: '',
    license: '',
    business_name: '',
    address: '',
    city: '',
    subdivision: '',
    postal_code: '',
    phone: '',
    email: '',
    user_id: '',
});

const storeVeterinarian = () => {
  form.post(route('veterinarians.store'), {
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
          :title="__('New Veterinarian')"
          :action="__('Create New Veterinarian')"
          :users="users"
          :canSubmit="false"
          @action-clicked="storeVeterinarian"
        />
      </div>
    </div>
  </AppLayout>
</template>
