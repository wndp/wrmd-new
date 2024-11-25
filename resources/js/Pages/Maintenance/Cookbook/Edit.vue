<script setup>
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from '../Partials/MaintenanceAside.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import CookbookForm from '../Partials/CookbookForm.vue';
import DeleteCookbookForm from '../Partials/DeleteCookbookForm.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  formula: {
    type: Object,
    required: true
  }
});

const form = useForm({
  name: props.formula.name,
  start_on_plan_date: props.formula.defaults?.start_on_plan_date || false,
  duration: props.formula.defaults?.duration,
  frequency: props.formula.defaults?.frequency,
  frequency_unit_id: props.formula.defaults?.frequency_unit_id,
  route_id: props.formula.defaults?.route_id,
  description: props.formula.defaults?.description,
  ingredients: props.formula.defaults?.ingredients || [],
});

const updateRecipe = () => {
  form.put(route('maintenance.cookbook.update', props.formula), {
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
          :title="__('Update Recipe')"
          :action="__('Update Recipe')"
          :canSubmit="false"
          @saved="updateRecipe"
        />
        <DeleteCookbookForm
          :formula="formula"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>
