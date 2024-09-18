<script setup>
import { computed } from 'vue';
import Alert from '@/Components/Alert.vue';
import ReportGroup from './ReportGroup.vue';
import chunk from 'lodash/chunk';
import {__} from '@/Composables/Translate';

const props = defineProps({
  reports: {
    type: Array,
    required: true
  }
});

let reportsChunks = computed(() => chunk(props.reports, Math.ceil(props.reports.length / 2)));
</script>

<template>
  <ReportGroup
    id="reports-favorite"
    :title="__('Favorite Reports')"
    :reports="reportsChunks"
  >
    <Alert v-if="reports.length === 0">
      {{ __("It's OK to play favorites. Select the stars to add the reports you use most often.") }}
    </Alert>
  </ReportGroup>
</template>
