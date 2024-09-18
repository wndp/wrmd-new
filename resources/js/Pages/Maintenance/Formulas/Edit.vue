<script setup>
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from '../Partials/MaintenanceAside.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import FormulaForm from '../Partials/FormulaForm.vue';
import DeleteFormulaForm from '../Partials/DeleteFormulaForm.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  formula: {
    type: Object,
    required: true
  }
});

const form = useForm({
  name: props.formula.name,
  drug: props.formula.defaults?.drug,
  concentration: props.formula.defaults?.concentration,
  concentration_unit_id: props.formula.defaults?.concentration_unit_id,
  dosage: props.formula.defaults?.dosage,
  dosage_unit_id: props.formula.defaults?.dosage_unit_id,
  loading_dose: props.formula.defaults?.loading_dose,
  loading_dose_unit_id: props.formula.defaults?.loading_dose_unit_id,
  dose: props.formula.defaults?.dose,
  dose_unit_id: props.formula.defaults?.dose_unit_id,
  frequency_id: props.formula.defaults?.frequency_id,
  route_id: props.formula.defaults?.route_id,
  auto_calculate_dose: props.formula.defaults?.auto_calculate_dose || false,
  start_on_prescription_date: props.formula.defaults?.start_on_prescription_date || false,
  duration: props.formula.defaults?.duration,
  is_controlled_substance: props.formula.defaults?.is_controlled_substance || false,
  instructions: props.formula.defaults?.instructions,
});

const updateFormula = () => {
  form.put(route('maintenance.formulas.update', props.formula), {
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
          :title="__('Update Prescription Formula')"
          :action="__('Update Prescription Formula')"
          :canSubmit="false"
          @saved="updateFormula"
        />
        <DeleteFormulaForm
          :formula="formula"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>
