<script setup>
import PatientLayout from '@/Layouts/PatientLayout.vue';
import DailyTasksTabs from './Partials/DailyTasksTabs.vue';
import Panel from '@/Components/Panel.vue';
import Task from '@/Pages/DailyTasks/Partials/Task.vue';
import { dateForHumans, dateInIso8601 } from '@/Composables/DateFormats.js';
import {__} from '@/Composables/Translate';

defineProps({
  pastDueTasks: {
    type: Array,
    required: true
  }
})
</script>

<template>
  <PatientLayout title="Daily Tasks">
    <DailyTasksTabs :patient="$page.props.admission.patient" />
    <Panel class="mt-8 md:mt-0 md:rounded-t-none">
      <template #title>
        {{ __('Past Due Tasks') }}
      </template>
      <template #description>
        {{ __('Below are the incomplete tasks for this patient scheduled for the past 7 days.') }}
      </template>
      <template #content>
        <div class="col-span-6 divide-y divide-gray-200 space-y-2">
          <div
            v-for="pastDue in pastDueTasks"
            :key="dateInIso8601(pastDue.date)"
          >
            <div class="mt-2 p-2 hover:bg-yellow-50">
              <h4 class="text-md leading-6 font-normal text-gray-900 mb-2">
                {{ dateForHumans(pastDue.date) }}
              </h4>
              <Task
                v-for="task in pastDue.tasks"
                :key="`${dateInIso8601(pastDue.date)}-${task.type_id}`"
                :task="task"
                :date="dateInIso8601(pastDue.date)"
              />
            </div>
          </div>
        </div>
      </template>
    </Panel>
  </PatientLayout>
</template>
