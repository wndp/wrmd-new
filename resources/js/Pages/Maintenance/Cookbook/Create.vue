<script setup>
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from '../Partials/MaintenanceAside.vue';
import CookbookForm from '../Partials/CookbookForm.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';

const form = useForm({
  name: '',
  start_on_plan_date: false,
  duration: '',
  frequency: '',
  frequency_unit_id: '',
  route_id: '',
  description: '',
  ingredients: [],
});

const storeRecipe = () => {
  form.post(route('maintenance.cookbook.store'), {
    preserveScroll: true
  });
};
</script>

<template>
  <AppLayout title="Cookbook">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <MaintenanceAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <Link
          :href="route('maintenance.cookbook.index')"
          class="mt-1 inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
        >
          <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
          {{ __('Return to Recipe List') }}
        </Link>
        <CookbookForm
          :form="form"
          :title="__('New Recipe')"
          :action="__('Create New Recipe')"
          :canSubmit="false"
          @saved="storeRecipe"
        />
      </div>
    </div>
  </AppLayout>
</template>
