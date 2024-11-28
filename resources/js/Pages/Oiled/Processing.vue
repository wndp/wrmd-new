<script setup>
import {computed} from 'vue';
import {useForm} from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import CollectionCard from './Partials/CollectionCard.vue';
import ReceivingCard from './Partials/ReceivingCard.vue';
import EvidenceCard from './Partials/EvidenceCard.vue';
import ProcessingCommentsCard from './Partials/ProcessingCommentsCard.vue';
import OilingDataCard from './Partials/OilingDataCard.vue';
import CarcassConditionCard from './Partials/CarcassConditionCard.vue';
import OutcomeCard from '@/Components/FormCards/OutcomeCard.vue';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
import {__} from '@/Composables/Translate';
import {AttributeOptionUiBehavior} from '@/Enums/AttributeOptionUiBehavior';
import omit from 'lodash/omit';

const props = defineProps({
  media: {
    type: Array,
    required: true
  },
  teamIsInPossession: {
    type: Boolean,
    required: true
  },
  patient: {
    type: Object,
    required: true
  },
  attributeOptionUiBehaviors: {
    type: Object,
    required: true
  },
  collectionConditionAliveId: {
    type: Number,
    required: true
  },
  collectionConditionDeadId: {
    type: Number,
    required: true
  }
});

const collectionForm = useForm({
  collected_at: props.patient.oil_processing?.collected_at,
  collection_condition_id: props.patient.oil_processing?.collection_condition_id || props.collectionConditionAliveId,
  collector_name: props.patient.rescuer.full_name,
  address_found: props.patient.address_found,
  lat_found: props.patient.lat_found,
  lng_found: props.patient.lng_found
});

const receivingForm = useForm({
  received_at_primary_care_at: props.patient.oil_processing?.received_at_primary_care_at || [],
  received_at_primary_care_by: props.patient.oil_processing?.received_at_primary_care_by,
  species_confirmed_by: props.patient.oil_processing?.species_confirmed_by
});

const evidenceForm = useForm({
  evidence_collected: props.patient.oil_processing?.evidence_collected || [],
  evidence_collected_by: props.patient.oil_processing?.evidence_collected_by,
  processed_at: props.patient.oil_processing?.processed_at
});

const commentsForm = useForm({
  comments: props.patient.oil_processing?.comments
});

const oilingDataForm = useForm({
  processed_by: props.patient.oil_processing?.processed_by,
  oiling_depth_id: props.patient.oil_processing?.oiling_depth_id,
  oiling_status_id: props.patient.oil_processing?.oiling_status_id,
  oiling_location_id: props.patient.oil_processing?.oiling_location_id,
  oiling_percentage_id: props.patient.oil_processing?.oiling_percentage_id,
  color_of_oil_id: props.patient.oil_processing?.color_of_oil_id,
  oil_condition_id: props.patient.oil_processing?.oil_condition_id,
});

const carcassConditionForm = useForm({
  carcass_condition_id: props.patient.oil_processing?.carcass_condition_id,
  extent_of_scavenging_id: props.patient.oil_processing?.extent_of_scavenging_id,
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
  is_carcass_saved: props.patient.is_carcass_saved,
});

const dispositionIsReleasedOrTransferred = computed(() => {
    return [
      props.attributeOptionUiBehaviors[AttributeOptionUiBehavior.PATIENT_DISPOSITION_IS_RELEASED][0],
      props.attributeOptionUiBehaviors[AttributeOptionUiBehavior.PATIENT_DISPOSITION_IS_TRANSFERRED][0]
    ].includes(
        Number(outcomeForm.disposition_id)
    );
});

const updateReceiving = () => {
  receivingForm.put(route('oiled.processing.receiving.update', {
    patient: props.patient,
  }), {
    preserveScroll: true,
  });
};

const updateCollection = () => {
  collectionForm.put(route('oiled.processing.collection.update', {
    patient: props.patient,
  }), {
    preserveScroll: true,
  });
};

const updateEvidence = () => {
  evidenceForm.put(route('oiled.processing.evidence.update', {
    patient: props.patient
  }), {
    preserveScroll: true,
  });
};

const updateComments = () => {
  commentsForm.put(route('oiled.processing.comments.update', {
    patient: props.patient
  }), {
    preserveScroll: true,
  });
};

const updateOilingData = () => {
  oilingDataForm.put(route('oiled.processing.oiling.update', {
    patient: props.patient
  }), {
    preserveScroll: true,
  });
};

const updateCarcassCondition = () => {
  carcassConditionForm.put(route('oiled.processing.carcassCondition.update', {
    patient: props.patient
  }), {
    preserveScroll: true,
  });
}

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
  });
};
</script>

<template>
  <PatientLayout :title="__('Processing')">
    <CollectionCard
      :form="collectionForm"
      :canSubmit="can(Abilities.MANAGE_OIL_PROCESSING) && patient.locked_at === null"
      class="mt-4"
      @submitted="updateCollection"
    />
    <ReceivingCard
      :form="receivingForm"
      :patientId="patient.id"
      :canSubmit="can(Abilities.MANAGE_OIL_PROCESSING) && patient.locked_at === null"
      class="mt-8"
      @submitted="updateReceiving"
    />
    <EvidenceCard
      :form="evidenceForm"
      :media="media"
      :patientId="patient.id"
      :canSubmit="can(Abilities.MANAGE_OIL_PROCESSING) && patient.locked_at === null"
      class="mt-8"
      @submitted="updateEvidence"
    />
    <ProcessingCommentsCard
      :form="commentsForm"
      :canSubmit="can(Abilities.MANAGE_OIL_PROCESSING) && patient.locked_at === null"
      class="mt-8"
      @submitted="updateComments"
    />
    <OilingDataCard
      :form="oilingDataForm"
      :canSubmit="can(Abilities.MANAGE_OIL_PROCESSING) && patient.locked_at === null"
      class="mt-8"
      @submitted="updateOilingData"
    />
    <CarcassConditionCard
      v-if="patient.oil_processing?.collection_condition_id === props.collectionConditionDeadId"
      :form="carcassConditionForm"
      :canSubmit="can(Abilities.MANAGE_OIL_PROCESSING) && patient.locked_at === null"
      class="mt-8"
      @submitted="updateCarcassCondition"
    />
    <OutcomeCard
      class="mt-8"
      :form="outcomeForm"
      :teamIsInPossession="teamIsInPossession"
      :attributeOptionUiBehaviors="attributeOptionUiBehaviors"
      :canSubmit="can(Abilities.MANAGE_OIL_PROCESSING) && patient.locked_at === null"
      @submitted="updateOutcome"
    />
  </PatientLayout>
</template>
