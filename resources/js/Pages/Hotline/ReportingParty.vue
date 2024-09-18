<script setup>
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import IncidentHeader from './Partials/IncidentHeader.vue';
import IncidentTabs from './Partials/IncidentTabs.vue';
import PersonCard from '@/Components/FormCards/PersonCard.vue';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const props = defineProps({
  incident: {
    type: Object,
    required: true
  },
  reportingParty: {
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

const form = useForm({
  entity_id: props.reportingParty.entity_id,
  organization: props.reportingParty.organization,
  first_name: props.reportingParty.first_name,
  last_name: props.reportingParty.last_name,
  phone: props.reportingParty.phone,
  alternate_phone: props.reportingParty.alternate_phone,
  email: props.reportingParty.email,
  subdivision: props.reportingParty.subdivision,
  city: props.reportingParty.city,
  address: props.reportingParty.address,
  county: props.reportingParty.county,
  postal_code: props.reportingParty.postal_code,
  notes: props.reportingParty.notes,
  no_solicitations: 'no_solicitations' in props.reportingParty ? props.reportingParty.no_solicitations : true,
  is_volunteer: props.reportingParty.is_volunteer,
  is_member: props.reportingParty.is_member,
})

const updateRescuer = () => {
  form.put(route('people.update', {
    person: props.reportingParty
  }), {
    preserveScroll: true,
    //onError: () => this.stopAutoSave()
  });
};
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
    <PersonCard
      :form="form"
      :canSubmit="can(Abilities.MANAGE_HOTLINE)"
      :affiliation="__('Reporting Party')"
      class="mt-8"
      @submitted="updateRescuer"
    />
  </AppLayout>
</template>
