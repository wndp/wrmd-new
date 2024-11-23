<script setup>
import PatientLayout from '@/Layouts/PatientLayout.vue';
import VitalsTable from './Partials/VitalsTable.vue';
import Panel from '@/Components/Panel.vue';
import MediaList from '@/Components/Media/MediaList.vue';
import CareLogCard from '@/Components/FormCards/CareLogCard.vue';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
import {MediaResource} from '@/Enums/MediaResource';
import {__} from '@/Composables/Translate';

defineProps({
  patient: {
    type: Object,
    required: true
  },
  logs: {
    type: Array,
    required: true
  },
  media: {
    type: Array,
    required: true
  }
});
</script>

<script>
  // export default {
  //   props: {
  //     media: {
  //       type: Array,
  //       required: true
  //     },
  //     logs: {
  //       type: Array,
  //       required: true
  //     }
  //   },
  //   computed: {
  //     patient() {
  //       return this.$page.props.admission.patient;
  //     }
  //   }
  // };
</script>

<template>
  <PatientLayout title="Case Summary">
    <div class="grid lg:grid-cols-2 gap-4">
      <VitalsTable :patient="patient" />
      <Panel>
        <template #title>
          {{ __('Evidence Photos') }}
        </template>
        <template #content>
          <MediaList
            :media="media"
            :resource="MediaResource.PATIENT"
            :resourceId="patient.id"
          />
        </template>
      </Panel>
    </div>
    <CareLogCard
      class="mt-8"
      :patient="patient"
      :logs="logs"
      :canSubmit="can(Abilities.MANAGE_CARE_LOGS) && patient.locked_at === null"
    />
  </PatientLayout>
</template>
