<script setup>
import PatientLayout from '@/Layouts/PatientLayout.vue';
import NecropsyMetaForm from './Partials/NecropsyMetaForm.vue';
import NecropsyMorphometricsForm from './Partials/NecropsyMorphometricsForm.vue';
import NecropsySystemsForm from './Partials/NecropsySystemsForm.vue';
import NecropsySummaryForm from './Partials/NecropsySummaryForm.vue';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

defineProps({
  patient: {
    type: Object,
    required: true
  },
  necropsySampleOtherId: {
    type: Number,
    required: true
  },
});
</script>

<template>
  <PatientLayout title="Necropsy">
    <NecropsyMetaForm
      :patientId="patient.id"
      :necropsy="patient.necropsy || {}"
      :canSubmit="can(Abilities.UPDATE_NECROPSY) && patient.locked_at === null"
    />
    <NecropsyMorphometricsForm
      :patientId="patient.id"
      :necropsy="patient.necropsy || {}"
      :canSubmit="can(Abilities.UPDATE_NECROPSY) && patient.locked_at === null"
      class="mt-8"
    />
    <NecropsySystemsForm
      :patientId="patient.id"
      :necropsy="patient.necropsy || {}"
      :canSubmit="can(Abilities.UPDATE_NECROPSY) && patient.locked_at === null"
      class="mt-8"
    />
    <NecropsySummaryForm
      :patientId="patient.id"
      :necropsy="patient.necropsy || {}"
      :necropsySampleOtherId="necropsySampleOtherId"
      :canSubmit="can(Abilities.UPDATE_NECROPSY) && patient.locked_at === null"
      class="mt-8"
    />
  </PatientLayout>
</template>
