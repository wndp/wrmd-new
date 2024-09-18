<script setup>
import {ref, onMounted} from 'vue';
import {router} from '@inertiajs/vue3';
import Task from './Task.vue';
import NewTaskMenu from './NewTaskMenu.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import { formatISO9075 } from 'date-fns';
import merge from 'lodash/merge';
import { PlusIcon } from '@heroicons/vue/24/outline';
import axios from 'axios';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const props = defineProps({
    patient: Object,
    slug: String,
    filters: Object,
    search: String,
    hide: Boolean
})

const tasksComplete = ref(false);
onMounted(() => tasksComplete.value = allTasksComplete());

const totalOccurrences = () => props.patient.tasks.reduce((sum, task) => sum + task.number_of_occurrences, 0);

const completeOccurrences = () => props.patient.tasks
    .flatMap(task => {
        return task.completed_occurrences.map(occurrence => {
            return taskFootprint(task, occurrence);
        });
    }).length;

const allTasksComplete = () => completeOccurrences() === totalOccurrences();

const someTasksComplete = () => completeOccurrences() > 0 && completeOccurrences() < totalOccurrences();

const tasksAreComplete = (task) => task.completed_occurrences.length === task.number_of_occurrences;

const showTask = (task) => ! (props.hide && tasksAreComplete(task));

const taskFootprint = (task, occurrence) => `${task.type_id}-${occurrence}`;

const onAllTasksChange = () => {
    if (tasksComplete.value) {
        markAllTasksComplete();
    } else {
        unMarkAllTasksComplete();
    }
};

const markAllTasksComplete = () =>
    axios.post(`/internal-api/daily-tasks/record/patient/${props.patient.patient_id}`, {
        completed_at: formatISO9075(new Date()),
        filters: merge(props.filters, {
            slug: props.slug
        })
    }).then(() => {
        router.reload({ only: ['taskGroups'] });
    });

const unMarkAllTasksComplete = () =>
    axios.delete(`/internal-api/daily-tasks/record/patient/${props.patient.patient_id}`, {
        data: {
            filters: merge(props.filters, {
                slug: props.slug
            })
        }
    })
    .then(() => {
        router.reload({ only: ['taskGroups'] });
    });
</script>

<template>
  <div
    v-if="! (hide && allTasksComplete())"
    class="mt-2 p-2 hover:bg-yellow-50"
  >
    <div class="flex items-center space-x-2">
      <NewTaskMenu :patientId="patient.patient_id">
        <PlusIcon class="w-5 h-5 text-blue-600" />
      </NewTaskMenu>
      <Checkbox
        v-if="can(Abilities.MANAGE_DAILY_TASKS)"
        :id="'id'+patient.patient_id"
        v-model="tasksComplete"
        :indeterminate.prop="!allTasksComplete() && someTasksComplete()"
        value="0"
        @change="onAllTasksChange"
      />
      <div class="md:flex w-full">
        <strong class="flex-1 block font-medium text-gray-600">
          <Link
            :href="patient.url"
            class="hover:underline"
          >
            {{ patient.case_number }} {{ patient.common_name }}
          </Link>
        </strong>
        <strong class="font-medium text-gray-600">
          {{ patient.identity }}
        </strong>
      </div>
    </div>
    <Task
      v-for="task in patient.tasks"
      v-show="showTask(task)"
      :key="task.model.updated_at"
      :task="task"
      :date="filters.date"
      :search="search"
    />
  </div>
</template>
