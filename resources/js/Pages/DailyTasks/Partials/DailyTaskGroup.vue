<script setup>
import {ref, onMounted} from 'vue';
import {router} from '@inertiajs/vue3';
import { formatISO9075 } from 'date-fns';
import PatientTasks from './PatientTasks.vue';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { ChevronDownIcon, InformationCircleIcon } from '@heroicons/vue/24/solid';
import merge from 'lodash/merge';
import reduce from 'lodash/reduce';
import sum from 'lodash/sum';
import axios from 'axios';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
import {
    CheckIcon, ListBulletIcon, PrinterIcon, ViewfinderCircleIcon
} from "@heroicons/vue/24/outline";

const props = defineProps({
    group: Object,
    hide: Boolean,
    search: String,
    filters: Object,
    hasFocus: Boolean
});

const emit = defineEmits(['focus', 'print']);

const allTasksComplete = ref(false);

// axios.delete(this.route('select-patient.destroy'), { maxRedirects: 0 });
const listPatients = () =>
    axios.post(route('select-patients.store'), {
        patients: props.group.patients.map(p => p.patient_id)
    }).then(() => {
        router.visit(route('patients.index', {
            list: 'selected-patients-list'
        }));
    });

const toggleAllTasks = () =>{
    if (isAllTasksCompleteInGroup()) {
        let requests = props.group.patients.map(patient => {
            return axios.delete(`/internal-api/daily-tasks/record/patient/${patient.patient_id}`, {
                data: {
                    filters: merge(props.filters, {
                        slug: props.group.slug
                    })
                }
            });
        });

        axios.all(requests).then(() => {
            router.reload({ only: ['taskGroups'] });
        });
    } else {
        let requests = props.group.patients.map(patient => {
            return axios.post(`/internal-api/daily-tasks/record/patient/${patient.patient_id}`, {
                completed_at: formatISO9075(new Date()),
                filters: merge(props.filters, {
                    slug: props.group.slug
                })
            });
        });

        axios.all(requests).then(() => {
            router.reload({ only: ['taskGroups'] });
        });
    }

    allTasksComplete.value = ! isAllTasksCompleteInGroup();
};

const isAllTasksCompleteInGroup = () => props.group.patients.every(patient => {
    return isAllPatientTasksComplete(patient);
});

const isAllPatientTasksComplete = (patient) => patient.tasks.every(task => {
    return task.number_of_occurrences === task.completed_occurrences.length;
});

const countCompletedTasks = () => reduce(props.group.patients, (carry, patient) => {
    return carry + sum(patient.tasks.map(task => task.completed_occurrences.length));
}, 0);

onMounted(() => allTasksComplete.value = isAllTasksCompleteInGroup());
</script>

<template>
  <div class="bg-white shadow rounded-b-lg divide-y divide-gray-200">
    <div class="flex px-4 py-5 sm:px-6">
      <Menu
        as="span"
        class="-ml-px relative block"
      >
        <MenuButton
          class="relative inline-flex items-center h-100 text-xl leading-6 font-medium text-gray-800 focus:z-10 focus:outline-none"
          :dusk="`${group.slug}-actions`"
        >
          {{ group.category }}
          <ChevronDownIcon
            class="h-6 w-6 flex-shrink-0 ml-2"
            aria-hidden="true"
          />
        </MenuButton>
        <transition
          enterActiveClass="transition ease-out duration-100"
          enterFromClass="transform opacity-0 scale-95"
          enterToClass="transform opacity-100 scale-100"
          leaveActiveClass="transition ease-in duration-75"
          leaveFromClass="transform opacity-100 scale-100"
          leaveToClass="transform opacity-0 scale-95"
        >
          <MenuItems class="origin-top-left absolute left-0 z-30 mt-2 -mr-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none divide-y-4 divide-gray-100">
            <div>
              <MenuItem v-slot="{ active }">
                <button
                  type="button"
                  :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex items-center w-full text-left px-4 py-2 text-sm']"
                  @click="$emit('focus', group.slug)"
                >
                  <ViewfinderCircleIcon :class="{'text-red-500': hasFocus, 'w-5 h-5 mr-2': true}" />
                  <span
                    v-if="hasFocus"
                    class="text-red-500"
                  >{{ __('Un-Focus Tasks') }}</span>
                  <span v-else>{{ __('Focus on These Tasks') }}</span>
                </button>
              </MenuItem>
              <MenuItem v-slot="{ active }">
                <button
                  type="button"
                  :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex items-center w-full text-left px-4 py-2 text-sm']"
                  @click="$emit('print', group.slug)"
                >
                  <PrinterIcon class="w-5 h-5 mr-2" />
                  {{ __('Print Tasks') }}
                </button>
              </MenuItem>
              <MenuItem v-slot="{ active }">
                <button
                  type="button"
                  :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex items-center w-full text-left px-4 py-2 text-sm']"
                  @click="listPatients()"
                >
                  <ListBulletIcon class="w-5 h-5 mr-2" />
                  {{ __('List All Patients in Group') }}
                </button>
              </MenuItem>
              <MenuItem
                v-if="can(Abilities.MANAGE_DAILY_TASKS)"
                v-slot="{ active }"
              >
                <button
                  type="button"
                  :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex items-center w-full text-left px-4 py-2 text-sm']"
                  @click="toggleAllTasks()"
                >
                  <CheckIcon class="w-5 h-5 mr-2" />
                  <span
                    v-if="isAllTasksCompleteInGroup()"
                    class="text-red-500"
                  >{{ __('Un-check All') }}</span>
                  <span v-else>{{ __('Mark All As Complete') }}</span>
                </button>
              </MenuItem>
            </div>
          </MenuItems>
        </transition>
      </Menu>
      <div
        v-if="hasFocus"
        class="flex items-center ml-4 text-red-500 text-md"
      >
        <InformationCircleIcon
          class="h-4 w-4 flex-shrink-0 mr-1"
          aria-hidden="true"
        />
        <span>{{ __('These tasks have focus.') }}</span>
      </div>
    </div>
    <div class="px-2 py-3 sm:px-4 sm:pb-4">
      <div class="divide-y divide-gray-200 space-y-2">
        <div
          v-for="patient in group.patients"
          :key="patient.patient_id + countCompletedTasks()"
        >
          <PatientTasks
            :patient="patient"
            :slug="group.slug"
            :filters="filters"
            :search="search"
            :hide="hide"
          />
        </div>
      </div>
    </div>
  </div>
</template>
