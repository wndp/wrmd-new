<script setup>
import { ref } from 'vue'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/solid';
import VitalModal from '../Partials/VitalModal.vue';
import DeleteVitalModal from '../Partials/DeleteVitalModal.vue';

defineProps({
  vital: Object
});

const showVitalModal = ref(false);
const showDeleteVitalModal = ref(false);
</script>

<template>
  <tr class="even:bg-gray-50">
    <td class="py-1 pl-4 pr-0 text-sm whitespace-nowrap">
      <button
        class="text-red-500 hover:text-red-900 mr-2"
        @click="showDeleteVitalModal=true"
      >
        <TrashIcon class="h-5 w-5" />
      </button>
      <button
        class="text-blue-600 hover:text-blue-900"
        @click="showVitalModal=true"
      >
        <PencilIcon class="h-5 w-5" />
      </button>
    </td>
    <td class="w-full max-w-0 py-1 px-4 text-sm font-medium text-gray-900 sm:w-auto sm:max-w-none">
      {{ vital.recorded_at_for_humans }}
      <dl class="font-normal xl:hidden mt-2">
        <div class="flex">
          <dt class="mr-2 text-sm font-bold text-gray-900">
            Weight
          </dt>
          <dd class="truncate text-sm text-gray-700">
            {{ vital.exam.weight }}<span v-if="vital.exam.weight">g</span>
          </dd>
        </div>
        <div class="flex">
          <dt class="mr-2 text-sm font-bold text-gray-900">
            Temp
          </dt>
          <dd class="truncate text-gray-500 sm:hidden">
            {{ vital.exam.temperature }}<span v-if="vital.exam.temperature">F</span>
          </dd>
        </div>
      </dl>
    </td>
    <td class="px-3 py-1 text-sm text-gray-500">
      <span class="hidden xl:inline">{{ vital.exam.weight }}</span>
      <dl class="font-normal xl:hidden mt-2">
        <div class="flex">
          <dt class="mr-2 text-sm font-bold text-gray-900">
            PCV
          </dt>
          <dd class="truncate text-sm text-gray-700">
            {{ vital.lab.data.pcv }}<span v-if="vital.lab.data.pcv">%</span>
          </dd>
        </div>
        <div class="flex">
          <dt class="mr-2 text-sm font-bold text-gray-900">
            TS
          </dt>
          <dd class="truncate text-gray-500 sm:hidden">
            {{ vital.lab.data.tp }}<span v-if="vital.lab.data.tp">g/dl</span>
          </dd>
        </div>
        <div class="flex">
          <dt class="mr-2 text-sm font-bold text-gray-900">
            Attitude
          </dt>
          <dd class="truncate text-gray-500 sm:hidden">
            {{ vital.exam.attitude }}
          </dd>
        </div>
      </dl>
    </td>
    <td class="hidden px-3 py-1 text-sm text-gray-500 xl:table-cell">
      {{ vital.exam.temperature }}
    </td>
    <td class="hidden px-3 py-1 text-sm text-gray-500 xl:table-cell">
      {{ vital.lab.data.pcv }}
    </td>
    <td class="hidden px-3 py-1 text-sm text-gray-500 xl:table-cell">
      {{ vital.lab.data.tp }}
    </td>
    <td class="hidden pl-3 pr-4 py-1 text-sm text-gray-500 xl:table-cell">
      {{ vital.exam.attitude }}
    </td>
  </tr>
  <VitalModal
    v-if="showVitalModal"
    :show="true"
    :vital="vital"
    @close="showVitalModal = false"
  />
  <DeleteVitalModal
    v-if="showDeleteVitalModal"
    :show="true"
    :vital="vital"
    @close="showDeleteVitalModal = false"
  />
</template>
