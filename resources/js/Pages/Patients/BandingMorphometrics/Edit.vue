<script setup>
import PatientLayout from '@/Layouts/PatientLayout.vue';
import ResearchBandingForm from './Partials/ResearchBandingForm.vue';
import ResearchAuxiliaryMarkerForm from './Partials/ResearchAuxiliaryMarkerForm.vue';
import ResearchRecaptureForm from './Partials/ResearchRecaptureForm.vue';
import ResearchMorphometricsForm from './Partials/ResearchMorphometricsForm.vue';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

defineProps({
  patient: {
    type: Object,
    required: true
  }
});
</script>

<template>
  <PatientLayout title="Banding & Morphometrics">
    <ResearchBandingForm
      :patientId="patient.id"
      :banding="patient.banding"
      :canSubmit="can(Abilities.UPDATE_BANDING_AND_MORPHOMETRICS) && patient.locked_at === null"
    />
    <ResearchAuxiliaryMarkerForm
      :patientId="patient.id"
      :banding="patient.banding"
      :canSubmit="can(Abilities.UPDATE_BANDING_AND_MORPHOMETRICS) && patient.locked_at === null"
      class="mt-8"
    />
    <ResearchRecaptureForm
      :patientId="patient.id"
      :banding="patient.banding"
      :canSubmit="can(Abilities.UPDATE_BANDING_AND_MORPHOMETRICS) && patient.locked_at === null"
      class="mt-8"
    />
    <ResearchMorphometricsForm
      :patientId="patient.id"
      :morphometric="patient.morphometric"
      :canSubmit="can(Abilities.UPDATE_BANDING_AND_MORPHOMETRICS) && patient.locked_at === null"
      class="mt-8"
    />
  </PatientLayout>
</template>
