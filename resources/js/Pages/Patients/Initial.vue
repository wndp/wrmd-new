<script setup>
import {computed} from 'vue';
import {usePage, useForm} from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import ExamCard from '@/Components/FormCards/ExamCard.vue';
import IntakeCard from '@/Components/FormCards/IntakeCard.vue';
import DiagnosisCard from '@/Components/FormCards/DiagnosisCard.vue';
import OutcomeCard from '@/Components/FormCards/OutcomeCard.vue';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
import omit from 'lodash/omit';
import {AttributeOptionUiBehavior} from '@/Enums/AttributeOptionUiBehavior';
import {SettingKey} from '@/Enums/SettingKey';

const props = defineProps({
  teamIsInPossession: {
    type: Boolean,
    required: true
  },
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
})

// const patient = computed(() => usePage().props.admission.patient);

const dispositionIsReleasedOrTransferred = computed(() => {
    return [
      props.attributeOptionUiBehaviors[AttributeOptionUiBehavior.PATIENT_DISPOSITION_IS_RELEASED][0],
      props.attributeOptionUiBehaviors[AttributeOptionUiBehavior.PATIENT_DISPOSITION_IS_TRANSFERRED][0]
    ].includes(
        Number(outcomeForm.disposition_id)
    );
});

const intakeForm = useForm({
  custom_values: props.patient.custom_values || {},
  transported_by: props.patient.transported_by,
  admitted_by: props.patient.admitted_by,
  found_at: props.patient.found_at,
  address_found: props.patient.address_found,
  city_found: props.patient.city_found,
  county_found: props.patient.county_found,
  subdivision_found: props.patient.subdivision_found,
  lat_found: props.patient.lat_found,
  lng_found: props.patient.lng_found,
  reason_for_admission: props.patient.reason_for_admission,
  care_by_rescuer: props.patient.care_by_rescuer,
  notes_about_rescue: props.patient.notes_about_rescue,
});

const examForm = useForm({
  custom_values: props.exam?.custom_values || {},
  examined_at: props.exam?.examined_at,
  exam_type_id: props.attributeOptionUiBehaviors[AttributeOptionUiBehavior.EXAM_TYPE_IS_INTAKE][0],
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

const diagnosisForm = useForm({
  custom_values: props.patient.custom_values || {},
  diagnosis: props.patient.diagnosis,
});

const outcomeForm = useForm({
  custom_values: props.patient.custom_values || {},
  disposition_id: props.patient.disposition_id,
  dispositioned_at: props.patient.dispositioned_at,
  release_type_id: props.patient.release_type_id,
  transfer_type_id: props.patient.transfer_type_id,
  disposition_address: props.patient.disposition_address,
  disposition_city: props.patient.disposition_city,
  disposition_subdivision: props.patient.disposition_subdivision,
  disposition_postal_code: props.patient.disposition_postal_code,
  disposition_lat: props.patient.disposition_lat,
  disposition_lng: props.patient.disposition_lng,
  reason_for_disposition: props.patient.reason_for_disposition,
  dispositioned_by: props.patient.dispositioned_by,
  is_carcass_saved: props.patient.is_carcass_saved
});

const updateIntake = () => {
  intakeForm.transform(data => {
    return usePage().props.settings[SettingKey.SHOW_GEOLOCATION_FIELDS]
      ? data
      : omit(data, ['lat_found', 'lng_found'])
  })
  .put(route('patients.intake.update', {
    patient: props.patient
  }), {
    preserveScroll: true,
    //onError: () => this.stopAutoSave()
  });
};

const updateExam = () => {
  let routeProps = props.exam?.id ? {
    patient: props.patient,
    exam: props.exam.id,
  } : {
    patient: props.patient
  };

  examForm.put(route('patients.intake_exam.update', routeProps), {
    preserveScroll: true,
    //onError: () => stopAutoSave()
  });
}

const updateDiagnosis = () => diagnosisForm.put(route('patients.diagnosis.update', {
  patient: props.patient,
}), {
  preserveScroll: true,
  //onError: () => this.stopAutoSave()
});

const updateOutcome = () => {
  outcomeForm.transform(data => {
    return dispositionIsReleasedOrTransferred.value
      ? data
      : omit(data, ['disposition_lat', 'disposition_lng'])
  })
  .put(route('patients.outcome.update', {
    patient: props.patient,
  }), {
    preserveScroll: true,
    //onError: () => this.stopAutoSave()
  });
};
</script>

<template>
  <PatientLayout title="Initial Care">
    <IntakeCard
      :form="intakeForm"
      :canSubmit="can(Abilities.UPDATE_PATIENT_CARE) && patient.locked_at === null"
      @submitted="updateIntake"
    />
    <ExamCard
      class="mt-8"
      :form="examForm"
      :canSubmit="can(Abilities.MANAGE_EXAMS) && patient.locked_at === null"
      :abnormalBodyPartFindingID="abnormalBodyPartFindingID"
      @submitted="updateExam"
    />
    <DiagnosisCard
      class="mt-8"
      :form="diagnosisForm"
      :canSubmit="can(Abilities.UPDATE_PATIENT_CARE) && patient.locked_at === null"
      @submitted="updateDiagnosis"
    />
    <OutcomeCard
      class="mt-8"
      :form="outcomeForm"
      :teamIsInPossession="teamIsInPossession"
      :attributeOptionUiBehaviors="attributeOptionUiBehaviors"
      :canSubmit="can(Abilities.UPDATE_PATIENT_CARE) && patient.locked_at === null"
      @submitted="updateOutcome"
    />
  </PatientLayout>
</template>
