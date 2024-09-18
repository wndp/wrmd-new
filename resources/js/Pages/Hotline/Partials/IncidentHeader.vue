<script setup>
import {ref, computed} from 'vue';
import {router} from '@inertiajs/vue3';
import {ArrowLongLeftIcon, ChevronDownIcon} from '@heroicons/vue/24/outline';
import {TransitionRoot, Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue'
import Badge from '@/Components/Badge.vue';
import DeleteIncidentModal from './DeleteIncidentModal.vue';
import RelateToOtherIncidentsModal from './RelateToOtherIncidentsModal.vue';
import RelateToExistingPatient from './RelateToExistingPatient.vue';
import {__} from '@/Composables/Translate';
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

const showConfirmingDeletion = ref(false);
const showRelateToExistingPatient = ref(false);
const showRelateToOtherIncidents = ref(false);

const badgeColor = computed(() => {
  switch (props.incident.incident_status_id) {
    case props.statusOpenId: return 'green';
    case props.statusUnresolvedId: return 'orange';
    case props.statusResolvedId: return 'yellow';
    default: return ''
  }
});

const admission = computed(() => {
  if (props.incident.patient) {
    return props.incident.patient.admissions.find(
      admission => admission.account_id === props.incident.account_id
    );
  }
  return {};
});

const caseQueryString = computed(() => {
  return {
    y: admission.value.case_year,
    c: admission.value.case_id,
  };
});

const admitAsPatient = () => router.get(route('patients.create', {incident: props.incident.id}));
</script>

<template>
  <div>
    <div class="sm:flex sm:justify-between sm:items-baseline">
      <div class="sm:w-0 sm:flex-1">
        <h1 class="text-2xl font-semibold text-gray-900">
          {{ __('Incident: :incidentNumber', {incidentNumber: incident.incident_number}) }}
        </h1>
        <Link
          :href="route('hotline.open.index')"
          class="mt-1 inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
        >
          <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
          {{ __('Return to Incidents List') }}
        </Link>
      </div>

      <div class="mt-4 flex items-center justify-between sm:mt-0 sm:ml-6 sm:flex-shrink-0 sm:justify-start">
        <button
          v-if="admission.case_number"
          class="inline-flex items-center bg-gray-200 rounded-full p-0.5 mr-2"
          dusk="incident-admission"
        >
          <Link :href="route('patients.continued.edit', caseQueryString)">
            <span class="mx-2 text-xs font-bold text-gray-600">{{ __('Patient') }}</span>
            <Badge color="blue">
              {{ admission.case_number }}
            </Badge>
          </Link>
        </button>
        <button
          class="inline-flex items-center bg-gray-200 rounded-full p-1.5"
        >
          <span class="mx-2 text-xs font-bold text-gray-600">{{ __('Status') }}</span>
          <Badge :color="badgeColor">
            {{ incident.status.value }}
          </Badge>
        </button>
        <Badge
          v-if="incident.deleted_at !== null"
          color="red"
          class="ml-3"
        >
          {{ __('Deleted') }}
        </Badge>
        <Menu
          as="div"
          class="ml-3 relative inline-block text-left"
        >
          <div>
            <MenuButton class="inline-flex justify-center items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-1 bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300">
              <span class="sr-only">{{ __('Actions') }}</span>
              <span>{{ __('Actions') }}</span>
              <ChevronDownIcon
                class="ml-1 h-5 w-5"
                aria-hidden="true"
              />
            </MenuButton>
          </div>
          <TransitionRoot
            enterActive="transition ease-out duration-100"
            enterFrom="transform opacity-0 scale-95"
            enterTo="transform opacity-100 scale-100"
            leaveActive="transition ease-in duration-75"
            leaveFrom="transform opacity-100 scale-100"
            leaveTo="transform opacity-0 scale-95"
          >
            <MenuItems class="origin-top-right absolute right-0 z-40 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
              <div class="py-1">
                <MenuItem
                  v-if="incident.patient === null && can(Abilities.CREATE_PATIENTS)"
                  v-slot="{ active }"
                >
                  <button
                    type="button"
                    :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'w-full flex justify-between px-4 py-2 text-sm']"
                    @click="admitAsPatient"
                  >
                    <span>{{ __('Admit as Patient') }}</span>
                  </button>
                </MenuItem>
                <MenuItem
                  v-if="incident.patient === null"
                  v-slot="{ active }"
                >
                  <button
                    type="button"
                    :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'w-full flex justify-between px-4 py-2 text-sm']"
                    @click="showRelateToExistingPatient = true"
                  >
                    <span>{{ __('Relate to Existing Patient') }}</span>
                  </button>
                </MenuItem>
                <!-- <MenuItem v-slot="{ active }">
                  <button
                    type="button"
                    :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'w-full flex justify-between px-4 py-2 text-sm']"
                    @click="showRelateToOtherIncidents = true"
                  >
                    <span>{{ __('Relate to Other Incidents') }}</span>
                  </button>
                </MenuItem> -->
                <MenuItem v-slot="{ active }">
                  <button
                    type="button"
                    :class="[active ? 'bg-red-100 text-red-900' : 'text-red-700', 'w-full flex justify-between px-4 py-2 text-sm']"
                    @click="showConfirmingDeletion = true"
                  >
                    <span v-if="incident.deleted_at !== null">{{ __('Restore Incident') }}</span>
                    <span v-else>{{ __('Delete Incident') }}</span>
                  </button>
                </MenuItem>
              </div>
            </MenuItems>
          </TransitionRoot>
        </Menu>
      </div>
    </div>
  </div>
  <DeleteIncidentModal
    :incident="incident"
    :show="showConfirmingDeletion"
    @close="showConfirmingDeletion = false"
  />
  <RelateToExistingPatient
    :incident="incident"
    :show="showRelateToExistingPatient"
    @close="showRelateToExistingPatient = false"
  />
  <RelateToOtherIncidentsModal
    :incident="incident"
    :show="showRelateToOtherIncidents"
    @close="showRelateToOtherIncidents = false"
  />
</template>
