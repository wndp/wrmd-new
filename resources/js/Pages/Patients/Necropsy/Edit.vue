<template>
  <PatientLayout title="Necropsy">
    <NecropsyMetaForm
      :necropsy="patient.necropsy"
      :can-submit="can(Abilities.UPDATE_NECROPSY) && patient.locked_at === null"
    />
    <NecropsyCarcassForm
      :necropsy="patient.necropsy"
      :can-submit="can(Abilities.UPDATE_NECROPSY) && patient.locked_at === null"
      class="mt-8"
    />
    <NecropsyMorphometricsForm
      :necropsy="patient.necropsy"
      :can-submit="can(Abilities.UPDATE_NECROPSY) && patient.locked_at === null"
      class="mt-8"
    />
    <NecropsySystemsForm
      :necropsy="patient.necropsy"
      :can-submit="can(Abilities.UPDATE_NECROPSY) && patient.locked_at === null"
      class="mt-8"
    />
    <NecropsySummaryForm
      :necropsy="patient.necropsy"
      :can-submit="can(Abilities.UPDATE_NECROPSY) && patient.locked_at === null"
      class="mt-8"
    />
  </PatientLayout>
</template>

<script setup>
import PatientLayout from '@/Layouts/PatientLayout.vue';
import NecropsyMetaForm from './Partials/NecropsyMetaForm.vue';
import NecropsyCarcassForm from './Partials/NecropsyCarcassForm.vue';
import NecropsyMorphometricsForm from './Partials/NecropsyMorphometricsForm.vue';
import NecropsySystemsForm from './Partials/NecropsySystemsForm.vue';
import NecropsySummaryForm from './Partials/NecropsySummaryForm.vue';
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
