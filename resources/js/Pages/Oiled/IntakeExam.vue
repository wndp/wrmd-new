<template>
  <PatientLayout title="Intake">
    <template #tabs>
      <OiledPatientTabs />
    </template>
    <ExamForm
      class="mt-4"
      :patient="patient"
      :exam="exam ?? undefined"
      :can-submit="!patient.is_frozen"
      :heading="__('Intake Exam')"
      exam-type="Intake"
      form-route="patients.intake_exam.update"
    />
  </PatientLayout>
</template>

<script setup>
import PatientLayout from '@/Layouts/PatientLayout.vue';
import OiledPatientTabs from './Partials/OiledPatientTabs.vue';
import ExamForm from '@/Components/Forms/ExamForm.vue';
</script>

<script>
  export default {
    props: {
      exam: {
        type: Object,
        default: () => ({})
      }
    },
    computed: {
      patient() {
        return this.$page.props.admission.patient;
      }
    }
  };
</script>
