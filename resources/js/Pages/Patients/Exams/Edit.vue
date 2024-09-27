<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import ExamCard from '@/Components/FormCards/ExamCard.vue';
import FormSection from '@/Components/FormElements/FormSection.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import DeleteExamModal from './Partials/DeleteExamModal.vue';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

defineProps({
  exam: {
    type: Object,
    required: true
  }
});

let confirmingExamDeletion = ref(false);

let patient = computed(() => usePage().props.admission.patient);
let caseQueryString = computed(() => {
  return {
    y: usePage().props.admission.case_year,
    c: usePage().props.admission.case_id,
  }
})
</script>

<template>
  <PatientLayout title="Exams">
    <div class="mt-4 sm:flex sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          {{ __('Update Daily Exam') }}
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
    <ExamCard
      class="mt-8"
      :patient="patient"
      :exam="exam"
      :can-submit="can(Abilities.MANAGE_EXAMS) && patient.locked_at === null"
      show-exam-type
    />
    <FormSection class="mt-8">
      <template #title>
        {{ __('Delete Exam') }}
      </template>

      <!-- <Alert
        class="col-span-4"
        color="red"
      >
        {{ __('Once a veterinarian is deleted, all of their and data will be permanently deleted. Before deleting this user, please download any data or information regarding this user that you wish to retain.') }}
      </Alert> -->

      <template #actions>
        <DangerButton
          @click="confirmingExamDeletion = true"
        >
          {{ __('Delete Exam') }}
        </DangerButton>
      </template>
    </FormSection>
    <DeleteExamModal
      v-if="confirmingExamDeletion"
      :patient="patient"
      :exam="exam"
      :show="true"
      @close="confirmingExamDeletion = false"
    />
  </PatientLayout>
</template>
