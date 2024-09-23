<template>
  <tr class="divide-x divide-gray-200">
    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-800">
      <div class="flex gap-4">
        <button
          v-if="can(Abilities.MANAGE_EXPENSES) && patient.locked_at === null"
          class="text-red-600 hover:text-red-900"
          dusk="delete-transaction"
          @click="showDeleteTransaction = true"
        >
          <TrashIcon class="w-5 h-5" />
        </button>
        <button
          v-if="can(Abilities.MANAGE_EXPENSES) && patient.locked_at === null"
          class="text-blue-600 hover:text-blue-900"
          dusk="edit-transaction"
          @click="showEditTransaction = true"
        >
          <PencilIcon class="w-5 h-5" />
        </button>
      </div>
    </td>
    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
      {{ transaction.transacted_at_for_humans }}
    </td>
    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
      {{ transaction.category.name }}
      <p class="mt-1 text-sm text-gray-500">
        {{ transaction.memo }}
      </p>
    </td>
    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 text-center">
      {{ transaction.debit ? transaction.debit_for_humans : '' }}
    </td>
    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 text-center">
      {{ transaction.credit ? transaction.credit_for_humans : '' }}
    </td>
  </tr>
  <DeleteTransactionModal
    v-if="showDeleteTransaction"
    :transaction="transaction"
    :show="true"
    @close="showDeleteTransaction = false"
  />
  <EditTransactionModal
    v-if="showEditTransaction"
    :transaction="transaction"
    :show="true"
    @close="showEditTransaction = false"
  />
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { TrashIcon, PencilIcon } from '@heroicons/vue/24/outline';
import DeleteTransactionModal from './DeleteTransactionModal.vue';
import EditTransactionModal from './EditTransactionModal.vue';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

defineProps({
  transaction: {
    type: Object,
    required: true
  }
})

let patient = computed(() => usePage().props.admission.patient);

let showDeleteTransaction = ref(false);
let showEditTransaction = ref(false);
</script>
