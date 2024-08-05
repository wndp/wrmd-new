<script setup>
import {ref} from 'vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  updated: Array,
  admitted: Array
});

const updatedPatients = ref(props.updated);
const admittedPatients = ref(props.admitted);
</script>

<template>
  <TabGroup as="div">
    <TabList class="border-b border-gray-200 mb-2">
      <Tab
        v-slot="{ selected }"
        as="template"
      >
        <button
          dusk="recent-update-patients"
          :class="[selected ? 'border-gray-500 text-gray-700' : 'border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300', 'w-1/2 py-4 px-1 text-center border-b-2 text-base']"
        >
          {{ __('Updated') }}
        </button>
      </Tab>
      <Tab
        v-slot="{ selected }"
        as="template"
      >
        <button
          dusk="recent-admitted-patients"
          :class="[selected ? 'border-gray-500 text-gray-700' : 'border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300', 'w-1/2 py-4 px-1 text-center border-b-2 text-base']"
        >
          {{ __('Admitted') }}
        </button>
      </Tab>
    </TabList>

    <TabPanels>
      <TabPanel>
        <ul>
          <li
            v-for="patient in updatedPatients"
            :key="patient.case_number"
          >
            <Link
              :href="patient.url"
              class="group flex items-center py-1 text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150 truncate"
            >
              {{ patient.case_number }} {{ patient.common_name }}
            </Link>
          </li>
        </ul>
      </TabPanel>
      <TabPanel>
        <ul>
          <li
            v-for="patient in admittedPatients"
            :key="patient.case_number"
          >
            <Link
              :href="patient.url"
              class="group flex items-center py-1 text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150 truncate"
            >
              {{ patient.case_number }} {{ patient.common_name }}
            </Link>
          </li>
        </ul>
      </TabPanel>
    </TabPanels>
  </TabGroup>
</template>
