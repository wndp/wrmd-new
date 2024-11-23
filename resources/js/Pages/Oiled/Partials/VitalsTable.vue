<script setup>
import { ref } from 'vue'
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import Panel from '@/Components/Panel.vue';
import { PlusIcon } from '@heroicons/vue/24/solid';
import VitalModal from '../Partials/VitalModal.vue';
import VitalsTableRow from './VitalsTableRow.vue';
import {__} from '@/Composables/Translate';

defineProps({
  patient: Object
});

const showVitalModal = ref(false);
</script>

<template>
  <Panel>
    <template #title>
      {{ __('Vitals') }}
    </template>
    <template #content>
      <div class="col-span-6 flex flex-col">
        <div class="-my-2 -mx-4 sm:-mx-6 lg:-mx-6">
          <div class="inline-block min-w-full py-2 align-middle">
            <div
              class="overflow-y-auto rounded-b-lg"
              style="max-height: 200px;"
            >
              <table class="min-w-full divide-y divide-gray-300">
                <thead>
                  <tr>
                    <th
                      scope="col"
                      class="sticky top-0 z-10 bg-gray-50 bg-opacity-90 py-3.5 pl-4 pr-0"
                      style="width: 80px"
                    >
                      <span class="sr-only">Edit</span>
                    </th>
                    <th
                      scope="col"
                      class="sticky top-0 z-10 bg-gray-50 bg-opacity-90 px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                      Date
                    </th>
                    <th
                      scope="col"
                      class="sticky top-0 z-10 bg-gray-50 bg-opacity-90 px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                      <span class="hidden xl:inline">Weight</span>
                    </th>
                    <th
                      scope="col"
                      class="hidden sticky top-0 z-10 bg-gray-50 bg-opacity-90 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 xl:table-cell"
                    >
                      Temp
                    </th>
                    <th
                      scope="col"
                      class="hidden sticky top-0 z-10 bg-gray-50 bg-opacity-90 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 xl:table-cell"
                    >
                      PCV
                    </th>
                    <th
                      scope="col"
                      class="hidden sticky top-0 z-10 bg-gray-50 bg-opacity-90 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 xl:table-cell"
                    >
                      TS
                    </th>
                    <th
                      scope="col"
                      class="hidden sticky top-0 z-10 bg-gray-50 bg-opacity-90 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 xl:table-cell"
                    >
                      Attitude
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <VitalsTableRow
                    v-for="vital in patient.vitals"
                    :key="vital.id"
                    :vital="vital"
                    class="even:bg-gray-50"
                  />
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #actions>
      <PrimaryButton
        class="flex items-center py-1"
        @click="showVitalModal = true"
      >
        <PlusIcon class="h-5 w-5 mr-2" /> Add Vital
      </PrimaryButton>
    </template>
  </Panel>
  <VitalModal
    v-if="showVitalModal"
    :show="true"
    @close="showVitalModal = false"
  />
</template>
