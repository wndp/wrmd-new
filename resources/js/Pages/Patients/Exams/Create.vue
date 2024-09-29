<script setup>
import {computed} from 'vue';
import {usePage, useForm} from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import ExamCard from '@/Components/FormCards/ExamCard.vue';
import {ArrowLongLeftIcon} from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const props = defineProps({
  patient: {
    type: Object,
    required: true
  },
  abnormalBodyPartFindingID: {
    type: Number,
    required: true
  },
});

const caseQueryString = computed(() => {
  return {
    y: usePage().props.admission.case_year,
    c: usePage().props.admission.case_id,
  }
});

const examForm = useForm({
  custom_values: props.exam?.custom_values || {},
  examined_at: props.exam?.examined_at,
  exam_type_id: props.exam_type_id,
  sex_id: props.exam?.sex_id,
  weight: props.exam?.weight || '',
  weight_unit_id: props.exam?.weight_unit_id,
  body_condition_id: props.exam?.body_condition_id,
  age: props.exam?.age,
  age_unit_id: props.exam?.age_unit_id,
  attitude_id: props.exam?.attitude_id,
  dehydration_id: props.exam?.dehydration_id,
  temperature: props.exam?.temperature,
  temperature_unit_id: props.exam?.temperature_unit_id,
  mucous_membrane_color_id: props.exam?.mucous_membrane_color_id,
  mucous_membrane_texture_id: props.exam?.mucous_membrane_texture_id,
  head: props.exam?.head,
  cns: props.exam?.cns,
  cardiopulmonary: props.exam?.cardiopulmonary,
  gastrointestinal: props.exam?.gastrointestinal,
  musculoskeletal: props.exam?.musculoskeletal,
  integument: props.exam?.integument,
  body: props.exam?.body,
  forelimb: props.exam?.forelimb,
  hindlimb: props.exam?.hindlimb,
  head_finding_id: props.exam?.head_finding_id,
  cns_finding_id: props.exam?.cns_finding_id,
  cardiopulmonary_finding_id: props.exam?.cardiopulmonary_finding_id,
  gastrointestinal_finding_id: props.exam?.gastrointestinal_finding_id,
  musculoskeletal_finding_id: props.exam?.musculoskeletal_finding_id,
  integument_finding_id: props.exam?.integument_finding_id,
  body_finding_id: props.exam?.body_finding_id,
  forelimb_finding_id: props.exam?.forelimb_finding_id,
  hindlimb_finding_id: props.exam?.hindlimb_finding_id,
  treatment: props.exam?.treatment,
  nutrition: props.exam?.nutrition,
  comments: props.exam?.comments,
  examiner: props.exam?.examiner,
});

const storeExam = () => {
  examForm.post(route('patients.exam.store', {
    patient: props.patient
  }), {
    preserveScroll: true,
  });
}
</script>

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
    <ExamCard
      class="mt-8"
      :form="examForm"
      :abnormalBodyPartFindingID="abnormalBodyPartFindingID"
      :canSubmit="can(Abilities.MANAGE_EXAMS) && patient.locked_at === null"
      showExamType
      @submitted="storeExam"
    />
  </PatientLayout>
</template>
