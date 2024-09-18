<script setup>
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from '../Partials/MaintenanceAside.vue';
import FormulaForm from '../Partials/FormulaForm.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';

const form = useForm({
  name: '',
  drug: '',
  concentration: '',
  concentration_unit_id: '',
  dosage: '',
  dosage_unit_id: '',
  loading_dose: '',
  loading_dose_unit_id: '',
  dose: '',
  dose_unit_id: '',
  frequency_id: '',
  route_id: '',
  auto_calculate_dose: false,
  start_on_prescription_date: false,
  duration: '',
  is_controlled_substance: false,
  instructions: '',
});

const storeFormula = () => {
  form.post(route('maintenance.formulas.store'), {
    preserveScroll: true
  });
};
</script>

<template>
  <AppLayout title="Formulary">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <MaintenanceAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <Link
          :href="route('maintenance.formulas.index')"
          class="mt-1 inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
        >
          <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
          {{ __('Return to Formula List') }}
        </Link>
        <FormulaForm
          :form="form"
          :title="__('New Prescription Formula')"
          :action="__('Create New Prescription Formula')"
          :canSubmit="false"
          @saved="storeFormula"
        />
      </div>
    </div>
  </AppLayout>
</template>
