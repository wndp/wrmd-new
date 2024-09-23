<template>
  <PatientLayout title="Banding & Morphometrics">
    <ResearchBandingForm
      :banding="patient.banding"
      :can-submit="can(Abilities.UPDATE_BANDING_AND_MORPHOMETRICS) && patient.locked_at === null"
    />
    <ResearchAuxiliaryMarkerForm
      :banding="patient.banding"
      :can-submit="can(Abilities.UPDATE_BANDING_AND_MORPHOMETRICS) && patient.locked_at === null"
      class="mt-8"
    />
    <ResearchRecaptureForm
      :banding="patient.banding"
      :can-submit="can(Abilities.UPDATE_BANDING_AND_MORPHOMETRICS) && patient.locked_at === null"
      class="mt-8"
    />
    <ResearchMorphometricsForm
      :morphometric="patient.morphometric"
      :can-submit="can(Abilities.UPDATE_BANDING_AND_MORPHOMETRICS) && patient.locked_at === null"
      class="mt-8"
    />
  </PatientLayout>
</template>

<script setup>
import PatientLayout from '@/Layouts/PatientLayout.vue';
import ResearchBandingForm from './Partials/ResearchBandingForm.vue';
import ResearchAuxiliaryMarkerForm from './Partials/ResearchAuxiliaryMarkerForm.vue';
import ResearchRecaptureForm from './Partials/ResearchRecaptureForm.vue';
import ResearchMorphometricsForm from './Partials/ResearchMorphometricsForm.vue';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
</script>

<script>
export default {
    computed: {
      patient() {
        return this.$page.props.admission.patient;
      }
    }
};
</script>
