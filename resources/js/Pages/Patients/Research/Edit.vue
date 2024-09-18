<template>
  <PatientLayout title="Banding & Morphometrics">
    <ResearchBandingForm
      :banding="patient.banding"
      :can-submit="can('update-research') && !patient.is_frozen"
    />
    <ResearchAuxiliaryMarkerForm
      :banding="patient.banding"
      :can-submit="can('update-research') && !patient.is_frozen"
      class="mt-8"
    />
    <ResearchRecaptureForm
      :banding="patient.banding"
      :can-submit="can('update-research') && !patient.is_frozen"
      class="mt-8"
    />
    <ResearchMorphometricsForm
      :morphometric="patient.morphometric"
      :can-submit="can('update-research') && !patient.is_frozen"
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
