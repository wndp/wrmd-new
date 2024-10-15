<script setup>
import {computed} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PersonTabs from './Partials/PersonTabs.vue';
import LocalStorage from '@/Composables/LocalStorage';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';

import {__} from '@/Composables/Translate';

const localStorage = LocalStorage();

defineProps({
  person: Object,
});

const uri = computed(() => localStorage.get('peopleFilters'));
</script>

<template>
  <AppLayout :title="person.identifier">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ person.identifier }}
      </h1>
      <Link
        :href="route('people.rescuers.index', uri)"
        class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150 mb-8"
      >
        <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
        {{ __('Return to People') }}
      </Link>
    </template>
    <PersonTabs :person="person" />
    <div class="flex flex-col mt-4">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
              <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
                <div class="ml-4 mt-4">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ __(":name's Hotline Incident's", {name: person.identifier}) }}
                  </h3>
                </div>
              </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Incident Number') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Date Reported') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Suspected Species') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Priority?') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Status') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="incident in person.hotline"
                  :key="incident.id"
                >
                  <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                    <Link
                      :href="route('hotline.incident.edit', { incident })"
                      class="hover:text-gray-900"
                    >
                      {{ incident.incident_number }}
                    </Link>
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                    {{ incident.reported_at_for_humans }}
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                    {{ incident.suspected_species }}
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                    {{ incident.is_priority ? __('Yes') : __('No') }}
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                    {{ incident.status }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
