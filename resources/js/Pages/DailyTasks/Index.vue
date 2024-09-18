<script setup>
import {ref, computed, onMounted, nextTick} from 'vue';
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DailyTaskFilters from './Partials/DailyTaskFilters.vue';
import DailyTaskGroup from './Partials/DailyTaskGroup.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import { PrinterIcon } from '@heroicons/vue/24/outline';
import omitBy from 'lodash/omitBy';
import isNil from 'lodash/isNil';
import axios from 'axios';
import {__} from '@/Composables/Translate';

const props = defineProps({
    taskGroups: {
        type: Array,
        required: true
    },
    filters: {
        type: Object,
        required: true
    },
});

const form = useForm({
    search: '',
    hide: false
});

const serachBody = ref(null);
const focus = ref('');
const normalizedSearch = computed(() => form.search.length > 2 ? form.search.toLowerCase() : '');
const filteredTasks = computed(() => {
    let groups = props.taskGroups;

    if (focus.value) {
        groups = groups.filter(group => group.slug === focus.value);
    }

    if (form.hide) {
        groups = groups.filter(group => {
            return ! allTasksCompleteInGroup(group);
        });
    }

    if (normalizedSearch.value) {
        groups = groups.filter(group => {
            return group.patients.some(patient => {
                return patient.tasks.some(task => {
                    return taskHasSearch(patient, task);
                });
            });
        })
        .map(group => {
            let n = Object.assign({}, group, {'patients': group.patients.filter(patient => {
                return patient.tasks.some(task => {
                    return taskHasSearch(patient, task);
                });
            })});
            return n;
        });
    }

    return groups;
});

const linkToPdf = computed(() => {
    let filters = omitBy(this.filters, isNil);
    filters = Object.keys(filters).map(key => key + '=' + filters[key]).join('&');
    return '/reports/generate/daily-tasks/?format=pdf&'+filters;
});

onMounted(() => {
    nextTick(() => {
        let main = document.getElementById('main');
        let top = serachBody.value.getBoundingClientRect().top - 65 -10;
        main.scrollTo({top: top, behavior: 'smooth'});
    });
});

const printDisplayedTasks = () => axios.post(linkToPdf.value);

// Consider adding focus.value to URI for bookmarking
const focusOn = (group) => focus.value = focus.value ? null : group;

const onPrint = (slug) => {
    let url = linkToPdf.value;
    if (slug) {
        url = `${url}&slug=${slug}`;
    }
    axios.post(url);
};

const taskHasSearch = (patient, task) => task.body.toLowerCase().match(normalizedSearch.value) ||
    patient.common_name.toLowerCase().match(normalizedSearch.value) ||
    patient.case_number.match(normalizedSearch.value);

const allTasksCompleteInGroup = (group) => group.patients.every(patient =>
    allPatientTasksComplete(patient)
);

const allPatientTasksComplete = (patient) => patient.tasks.every(task =>
    task.number_of_occurrences === task.completed_occurrences.length
);
</script>

<template>
  <AppLayout title="Daily Tasks">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 mb-8">
        {{ __('Daily Tasks') }}
      </h1>
    </template>
    <DailyTaskFilters
      :filters="filters"
    />
    <div
      id="serachBody"
      ref="serachBody"
      class="px-4 py-5 sm:px-6 mt-4 bg-white overflow-hidden shadow rounded-lg"
    >
      <div class="sm:flex sm:items-center">
        <div class="flex-1 mt-1 sm:mt-0 sm:mr-4">
          <TextInput
            v-model="form.search"
            name="search"
            :placeholder="__('Search tasks')+'...'"
          />
        </div>
        <div class="flex items-center sm:justify-end mt-4 sm:mt-0">
          <Toggle
            v-model="form.hide"
            dusk="completed"
            class="mr-2"
            :label="__('Hide Completed Patients')"
          />
        </div>
      </div>
      <div class="sm:flex sm:items-center mt-3 space-x-4">
        <PrimaryButton
          class="flex items-center text-blue-600 text-base"
          @click="printDisplayedTasks"
        >
          <PrinterIcon class="h-4 w-4 mr-1" />
          {{ __('Print displayed tasks') }}
        </PrimaryButton>
        <!-- <Link
          href="/"
          class="flex items-center text-blue-600 text-base"
        >
          <ClockIcon class="h-4 w-4 mr-1" />
          {{ __('Show past due tasks') }}
        </Link> -->
      </div>
    </div>

    <div class="space-y-4 mt-4">
      <DailyTaskGroup
        v-for="group in filteredTasks"
        :key="group.slug"
        :filters="filters"
        :group="group"
        :hide="form.hide"
        :search="normalizedSearch"
        :hasFocus="group.slug === focus"
        @focus="focusOn"
        @print="onPrint"
      />
    </div>
  </AppLayout>
</template>
