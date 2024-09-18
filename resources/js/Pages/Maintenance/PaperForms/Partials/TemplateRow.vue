<template>
  <tr>
    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-800">
      <button
        class="text-red-600 hover:text-red-900"
        dusk="delete-template"
        @click="showDeleteTemplate = true"
      >
        <TrashIcon class="w-5 h-5" />
      </button>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
      {{ template.name }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
      {{ template.created_at }}
    </td>
  </tr>
  <DeleteTemplateModal
    v-if="showDeleteTemplate"
    :template="template"
    :show="true"
    @close="showDeleteTemplate = false"
  />
</template>

<script setup>
import { ref } from 'vue';
import { TrashIcon } from '@heroicons/vue/24/outline';
import DeleteTemplateModal from './DeleteTemplateModal.vue';

defineProps({
  template: {
    type: Object,
    required: true
  }
})

let showDeleteTemplate = ref(false);
</script>
