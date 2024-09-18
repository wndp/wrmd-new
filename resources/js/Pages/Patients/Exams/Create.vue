<template>
  <PatientLayout title="Exams">
    <div class="mt-4 sm:flex sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          {{ __('New Daily Exam') }}
        </h3>
        <Link
          :href="route('patients.exam.index', caseQueryString)"
          class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
        >
          <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
          {{ __('Return to Exams List') }}
        </Link>
      </div>
    </div>
    <ExamForm
      class="mt-8"
      :patient="patient"
      :can-submit="can('manage-exams') && !patient.is_frozen"
      form-method="post"
      form-route="patients.exam.store"
      show-exam-type
      disable-auto-save
    />
  </PatientLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import ExamForm from '@/Components/Forms/ExamForm.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';

let patient = computed(() => usePage().props.admission.patient);
let caseQueryString = computed(() => {
  return {
    y: usePage().props.admission.case_year,
    c: usePage().props.admission.case_id,
  }
})
</script>
