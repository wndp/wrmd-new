<script setup>
import {ref, onMounted} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PeopleTabs from '../Partials/PeopleTabs.vue';
import URI from 'urijs';
import LocalStorage from '@/Composables/LocalStorage';
import {__} from '@/Composables/Translate';

const localStorage = LocalStorage();

defineProps({
  people: Array,
  sentence: String
})

const uriQuery = ref([]);

onMounted(() => {
  localStorage.store('peopleFilters', new URI().query(true));
  uriQuery.value = new URI().query(true)
});
</script>

<template>
  <AppLayout title="Combine People">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ __('People') }}
      </h1>
    </template>
    <PeopleTabs class="mt-4" />
    <div class="flex flex-col mt-8">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200">
            <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
              <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
                <div class="ml-4 mt-4">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ __('Search Results') }}
                  </h3>
                  <p class="mt-1 text-sm text-gray-500">
                    {{ __('The matches for these people have the same') }} {{ sentence }}
                  </p>
                </div>
                <div class="ml-4 mt-4 flex-shrink-0">
                  <Link
                    :href="route('people.combine.search')"
                    class="relative inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                  >
                    {{ __('Start Over') }}
                  </Link>
                </div>
              </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Matches') }}
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Match') }}
                  </th>
                  <th
                    scope="col"
                    class="relative px-6 py-3"
                  />
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="person in people"
                  :key="person.id"
                >
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                    {{ person.aggregate }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ person.match }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <Link
                      :href="route('people.combine.review', {person: person.id, ...uriQuery})"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      {{ __('Review These People') }}
                    </Link>
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
