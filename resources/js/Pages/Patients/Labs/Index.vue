<script setup>
import {ref} from 'vue';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import {ChevronDownIcon} from '@heroicons/vue/24/outline';
import {TransitionRoot, Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue'
import LabResult from './Partials/LabResult.vue';
import FecalResult from './Partials/FecalResult.vue';
import CytologyResult from './Partials/CytologyResult.vue';
import CbcResult from './Partials/CbcResult.vue';
import ChemistryResult from './Partials/ChemistryResult.vue';
import UrinalysisResult from './Partials/UrinalysisResult.vue';
import ToxicologyResult from './Partials/ToxicologyResult.vue';
import {__} from '@/Composables/Translate';

defineProps({
  patient: {
    type: Object,
    required: true
  },
  labReports: {
    type: Array,
    required: true
  }
});

const analysisTypes = ref([
  { name: __('Fecal Analysis'), component: 'fecal', can: true },
  { name: __('CBC (Complete Blood Count)'), component: 'cbc', can: true },
  { name: __('Blood Chemistry'), component: 'chemistry', can: true },
  { name: __('Cytology Analysis'), component: 'cytology', can: true },
  { name: __('Urinalysis'), component: 'urinalysis', can: true },
  { name: __('Toxicology Analysis'), component: 'toxicology', can: true },
].filter(o => o.can));

const showLabResult = ref(null);
</script>

<template>
  <PatientLayout title="Lab">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-xl font-semibold text-gray-900">
          {{ __('Lab Tests') }}
        </h1>
        <p class="mt-2 text-sm text-gray-700">
          A list of all the lab results for this patient including the date and the name of the technician.
        </p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <Menu
          as="div"
          class="relative inline-block text-left"
        >
          <div>
            <MenuButton class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300">
              <span class="sr-only">{{ __('Open options') }}</span>
              <span>{{ __('Add Lab Results') }}</span>
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
            <MenuItems class="absolute origin-top-left sm:origin-top-right left-0 sm:left-auto sm:right-0 z-40 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
              <div class="py-1">
                <MenuItem
                  v-for="item in analysisTypes"
                  :key="item.name"
                  v-slot="{ active }"
                >
                  <button
                    type="button"
                    :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'w-full flex justify-between px-4 py-2 text-sm']"
                    @click="showLabResult = item.component"
                  >
                    <span>{{ item.name }}</span>
                  </button>
                </MenuItem>
              </div>
            </MenuItems>
          </TransitionRoot>
        </Menu>
      </div>
    </div>
    <div class="mt-8 flex flex-col">
      <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full align-middle py-2 px-4 md:px-6 lg:px-8">
          <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="py-3.5 pl-4 pr-3 sm:pl-6 text-left text-xs font-medium text-gray-700 uppercase tracking-wider w-[50px]"
                  />
                  <th
                    scope="col"
                    class="px-3 py-3.5 text-left text-xs font-medium text-gray-700 uppercase tracking-wider w-[175px]"
                  >
                    {{ __('Date') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3.5 text-left text-xs font-medium text-gray-700 uppercase tracking-wider w-[175px]"
                  >
                    {{ __('Analysis') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3.5 text-left text-xs font-medium text-gray-700 uppercase tracking-wider w-[175px]"
                  >
                    {{ __('Technician') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3.5 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    {{ __('Comments') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white">
                <LabResult
                  v-for="(labReport, labIdx) in labReports"
                  :key="labReport.id"
                  :patientId="patient.id"
                  :labReport="labReport"
                  :class="labIdx % 2 === 0 ? undefined : 'bg-gray-50'"
                />
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <FecalResult
      v-if="showLabResult === 'fecal'"
      :patientId="patient.id"
      :show="true"
      @close="showLabResult = null"
    />
    <CytologyResult
      v-if="showLabResult === 'cytology'"
      :patientId="patient.id"
      :show="true"
      @close="showLabResult = null"
    />
    <CbcResult
      v-if="showLabResult === 'cbc'"
      :patientId="patient.id"
      :show="true"
      @close="showLabResult = null"
    />
    <ChemistryResult
      v-if="showLabResult === 'chemistry'"
      :patientId="patient.id"
      :show="true"
      @close="showLabResult = null"
    />
    <UrinalysisResult
      v-if="showLabResult === 'urinalysis'"
      :patientId="patient.id"
      :show="true"
      @close="showLabResult = null"
    />
    <ToxicologyResult
      v-if="showLabResult === 'toxicology'"
      :patientId="patient.id"
      :show="true"
      @close="showLabResult = null"
    />
  </PatientLayout>
</template>
