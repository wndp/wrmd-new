<template>
  <AppLayout title="Custom Fields">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <MaintenanceAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <Link
          :href="route('maintenance.custom_fields.index')"
          class="mt-1 inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
        >
          <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
          {{ __('Return to Custom Fields List') }}
        </Link>
        <CustomFieldForm
          v-model="form.customField"
          :title="__('New Custom Field')"
          :action="__('Create New Custom Field')"
          :can-submit="false"
          :errors="form.errors"
          @saved="store"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from '../Partials/MaintenanceAside.vue';
import CustomFieldForm from './Partials/CustomFieldForm.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';

const route = inject('route');

const form = useForm({
  customField: {
    group: 'Patient',
    panel: 'Intake',
    location: 'Top',
    type: 'text'
  }
});

const store = () => {
  form.transform(data => ({
    ...data.customField
  })).post(route('maintenance.custom_fields.store'), {
    preserveState: true
  });
};
</script>

