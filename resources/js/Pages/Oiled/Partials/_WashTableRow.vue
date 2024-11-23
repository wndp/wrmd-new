<template>
  <tr>
    <td class="px-4 py-4 text-sm whitespace-nowrap">
      <button
        class="text-red-500 hover:text-red-900 mr-2"
        @click="showDeleteWashModal=true"
      >
        <TrashIcon class="h-5 w-5" />
      </button>
      <button
        class="text-blue-600 hover:text-blue-900"
        dusk="edit-wash"
        @click="showWashModal = true"
      >
        <PencilIcon class="w-5 h-5" />
      </button>
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
      {{ wash.washed_at_for_humans }}
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
      <p class="text-gray-700">
        {{ wash.pre_treatment }}
      </p>
      <span v-if="wash.pre_treatment_duration">
        {{ __('Minutes') }}: {{ wash.pre_treatment_duration }}
      </span>
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
      <p class="text-gray-700">
        {{ wash.wash_type }}
      </p>
      <span v-if="wash.pre_treatment_duration">
        {{ __('Minutes') }}: {{ wash.pre_treatment_duration }}
      </span>
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
      {{ wash.detergent }}
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
      <p class="text-gray-700">
        {{ wash.drying_method }}
      </p>
      <span v-if="wash.drying_duration">
        {{ __('Minutes') }}: {{ wash.drying_duration }}
      </span>
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
      <dl class="font-normal">
        <div class="flex">
          <dt class="text-gray-700 mr-2">
            Washer(s)
          </dt>
          <dd class="truncate">
            {{ wash.washer }}
          </dd>
        </div>
        <div class="flex">
          <dt class="text-gray-700 mr-2">
            Handler(s)
          </dt>
          <dd class="truncate">
            {{ wash.handler }}
          </dd>
        </div>
        <div class="flex">
          <dt class="text-gray-700 mr-2">
            Rinser(s)
          </dt>
          <dd class="truncate">
            {{ wash.rinser }}
          </dd>
        </div>
        <div class="flex">
          <dt class="text-gray-700 mr-2">
            Dryer(s)
          </dt>
          <dd class="truncate">
            {{ wash.dryer }}
          </dd>
        </div>
      </dl>
    </td>
  </tr>
  <WashModal
    v-if="showWashModal"
    :show="true"
    :wash="wash"
    @close="showWashModal = false"
  />
  <DeleteWashModal
    v-if="showDeleteWashModal"
    :show="true"
    :wash="wash"
    @close="showDeleteWashModal = false"
  />
</template>

<script setup>
import { ref } from 'vue'
import { TrashIcon, PencilIcon } from '@heroicons/vue/24/solid';
import WashModal from './WashModal.vue';
import DeleteWashModal from '../Partials/DeleteWashModal.vue';

defineProps({
  wash: Object
});

const showWashModal = ref(false);
const showDeleteWashModal = ref(false);
</script>
