<script setup>
import {nextTick} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import DailyTasksTabs from './Partials/DailyTasksTabs.vue';
import Panel from '@/Components/Panel.vue';
import Task from '@/Pages/DailyTasks/Partials/Task.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  filters: {
    type: Object,
    required: true
  },
  tasks: {
    type: Array,
    required: true
  },
});

const form = useForm({
  date: props.filters.date,
  c: usePage().props.admission.case_id,
  y: usePage().props.admission.case_year
});

const filter = () => nextTick(() => {
  form.get(route('patients.daily-tasks.edit'));
});
</script>

<template>
  <PatientLayout title="Daily Tasks">
    <DailyTasksTabs />
    <Panel class="mt-8 md:mt-0 md:rounded-t-none">
      <template #title>
        <span class="flex items-center">
          {{ __('Tasks Due on:') }}
          <DatePicker
            id="date"
            v-model="form.date"
            class="ml-2"
            :configs="{maxDate: null}"
            @change="filter"
          />
        </span>
      </template>
      <template #content>
        <Task
          v-for="task in tasks"
          :key="task.type_id"
          :task="task"
          :date="form.date"
          class="col-span-6"
        />
      </template>
    </Panel>
  </PatientLayout>
</template>
