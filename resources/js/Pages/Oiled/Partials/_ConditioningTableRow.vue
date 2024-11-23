<template>
  <tr>
    <td class="px-4 py-4 text-sm whitespace-nowrap">
      <button
        class="text-red-500 hover:text-red-900 mr-2"
        @click="showDeleteConditioningModal=true"
      >
        <TrashIcon class="h-5 w-5" />
      </button>
      <button
        class="text-blue-600 hover:text-blue-900"
        dusk="edit-wash"
        @click="showConditioningModal = true"
      >
        <PencilIcon class="w-5 h-5" />
      </button>
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
      {{ conditioning.evaluated_at_for_humans }}
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
      {{ conditioning.buoyancy }}
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
      {{ conditioning.hauled_out }}
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
      {{ conditioning.preening }}
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
      <dl class="font-normal">
        <div class="flex">
          <dt class="text-gray-700 mr-2">
            {{ __('Self Feeding') }}
          </dt>
          <dd class="truncate">
            {{ conditioning.is_self_feeding ? __('Yes') : __('No') }}
          </dd>
        </div>
        <div class="flex">
          <dt class="text-gray-700 mr-2">
            {{ __('Flighted') }}
          </dt>
          <dd class="truncate">
            {{ conditioning.is_flighted ? __('Yes') : __('No') }}
          </dd>
        </div>
      </dl>
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
      {{ (conditioning.areas_wet_to_skin || []).join(', ') }}
    </td>
  </tr>
  <ConditioningModal
    v-if="showConditioningModal"
    :show="true"
    :conditioning="conditioning"
    @close="showConditioningModal = false"
  />
  <DeleteConditioningModal
    v-if="showDeleteConditioningModal"
    :show="true"
    :conditioning="conditioning"
    @close="showDeleteConditioningModal = false"
  />
</template>

<script setup>
import { ref } from 'vue'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/solid';
import ConditioningModal from './ConditioningModal.vue';
import DeleteConditioningModal from './DeleteConditioningModal.vue';

defineProps({
  conditioning: Object
});

const showConditioningModal = ref(false);
const showDeleteConditioningModal = ref(false);
</script>
