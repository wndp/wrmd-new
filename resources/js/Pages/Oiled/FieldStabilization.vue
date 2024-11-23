<script setup>
import {useForm} from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import ExamCard from '@/Components/FormCards/ExamCard.vue';
import {AttributeOptionUiBehavior} from '@/Enums/AttributeOptionUiBehavior';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
import {__} from '@/Composables/Translate';

const props = defineProps({
  patient: {
    type: Object,
    required: true
  },
  exam: {
    type: Object,
    default: () => ({})
  },
  abnormalBodyPartFindingID: {
    type: Number,
    required: true
  },
  attributeOptionUiBehaviors: {
    type: Object,
    required: true
  }
});

const examForm = useForm({
  custom_values: props.exam?.custom_values || {},
  examined_at: props.exam?.examined_at,
  exam_type_id: props.attributeOptionUiBehaviors[AttributeOptionUiBehavior.EXAM_TYPE_IS_FIELD][0],
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

const updateExam = () => {
  let routeProps = props.exam?.id ? {
    patient: props.patient,
    exam: props.exam.id,
  } : {
    patient: props.patient
  };

  examForm.put(route('patients.intake_exam.update', routeProps), {
    preserveScroll: true,
  });
}
</script>

<template>
  <PatientLayout title="Field Stabilization">
    <ExamCard
      class="mt-8"
      :form="examForm"
      :canSubmit="can(Abilities.MANAGE_EXAMS) && patient.locked_at === null"
      :abnormalBodyPartFindingID="abnormalBodyPartFindingID"
      :heading="__('Field Stabilization Exam')"
      @submitted="updateExam"
    />
  </PatientLayout>
</template>
