<script setup>
import {computed} from 'vue';
import {usePage, useForm} from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import DiagnosisCard from '@/Components/FormCards/DiagnosisCard.vue';
import CareLogCard from '@/Components/FormCards/CareLogCard.vue';
import OutcomeCard from '@/Components/FormCards/OutcomeCard.vue';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
import omit from 'lodash/omit';
import {AttributeOptionUiBehavior} from '@/Enums/AttributeOptionUiBehavior';

const props = defineProps({
  teamIsInPossession: {
    type: Boolean,
    required: true
  },
  patient: {
    type: Object,
    required: true
  },
  logs: {
    type: Array,
    required: true
  },
  attributeOptionUiBehaviors: {
    type: Object,
    required: true
  }
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

const dispositionIsReleasedOrTransferred = computed(() => {
    return [
      props.attributeOptionUiBehaviors[AttributeOptionUiBehavior.PATIENT_DISPOSITION_IS_RELEASED][0],
      props.attributeOptionUiBehaviors[AttributeOptionUiBehavior.PATIENT_DISPOSITION_IS_TRANSFERRED][0]
    ].includes(
        Number(outcomeForm.disposition_id)
    );
});

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
  <PatientLayout title="Continued Care">
    <DiagnosisCard
      :form="diagnosisForm"
      :canSubmit="can(Abilities.UPDATE_PATIENT_CARE) && patient.locked_at === null"
      @submitted="updateDiagnosis"
    />
    <CareLogCard
      class="mt-8"
      :patient="patient"
      :logs="logs"
      :canSubmit="can(Abilities.MANAGE_CARE_LOGS) && patient.locked_at === null"
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
