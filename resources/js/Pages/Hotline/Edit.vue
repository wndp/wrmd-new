<script setup>
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import IncidentHeader from './Partials/IncidentHeader.vue';
import IncidentTabs from './Partials/IncidentTabs.vue';
import IncidentMetaForm from './Partials/IncidentMetaForm.vue';
import IncidentDescriptionForm from './Partials/IncidentDescriptionForm.vue';
import IncidentResolutionForm from './Partials/IncidentResolutionForm.vue';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const props = defineProps({
  incident: {
    type: Object,
    required: true
  },
  statusOpenId: {
    type: Number,
    required: true
  },
  statusUnresolvedId: {
    type: Number,
    required: true
  },
  statusResolvedId: {
    type: Number,
    required: true
  }
});

const metaForm = useForm({
  reported_at: props.incident.reported_at_local,
  occurred_at: props.incident.occurred_at_local,
  recorded_by: props.incident.recorded_by,
  duration_of_call: props.incident.duration_of_call,
  suspected_species: props.incident.suspected_species,
  number_of_animals: props.incident.number_of_animals,
  category_id: props.incident.category_id,
  is_priority:  props.incident.is_priority,
  incident_status_id: props.incident.incident_status_id,
});

const descriptionForm = useForm({
  incident_address: props.incident.incident_address,
  incident_city: props.incident.incident_city,
  incident_subdivision: props.incident.incident_subdivision,
  incident_postal_code: props.incident.incident_postal_code,
  description: props.incident.description,
});

const resolutionForm = useForm({
  resolved_at: props.incident.resolved_at_local,
  given_information: props.incident.given_information,
  resolution: props.incident.resolution,
});

const updateMeta = () => metaForm.put(route('hotline.incident.update', {
  incident: props.incident,
}), {
  preserveState: false,
  //onError: () => this.stopAutoSave()
});

const updateDescription = () => descriptionForm.put(route('hotline.incident.update.description', {
  incident: props.incident,
}), {
  preserveScroll: true,
  //onError: () => this.stopAutoSave()
});

const updateResolution = () => resolutionForm.put(route('hotline.incident.update.resolution', {
  incident: props.incident,
}), {
  preserveState: false,
  //onError: () => this.stopAutoSave()
});
</script>

<template>
  <AppLayout title="Hotline">
    <IncidentHeader
      :incident="incident"
      :statusOpenId="statusOpenId"
      :statusUnresolvedId="statusUnresolvedId"
      :statusResolvedId="statusResolvedId"
    />
    <IncidentTabs
      :incident="incident"
      class="mt-4"
    />
    <IncidentMetaForm
      :form="metaForm"
      :canSubmit="can(Abilities.MANAGE_HOTLINE)"
      class="mt-8"
      @submitted="updateMeta"
    />
    <IncidentDescriptionForm
      :form="descriptionForm"
      :canSubmit="can(Abilities.MANAGE_HOTLINE)"
      class="mt-8"
      @submitted="updateDescription"
    />
    <IncidentResolutionForm
      :form="resolutionForm"
      :canSubmit="can(Abilities.MANAGE_HOTLINE)"
      class="mt-8"
      @submitted="updateResolution"
    />
  </AppLayout>
</template>
