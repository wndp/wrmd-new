<script setup>
import {__} from '@/Composables/Translate';

defineProps({
  formulas: {
    type: Object,
    required: true
  }
})
</script>

<template>
  <div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
        <div class="shadow overflow-hidden border-b border-gray-200">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-blue-100">
              <tr>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  {{ __('Formula Name') }}
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  {{ __('Formula Defaults') }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="formula in formulas.data"
                :key="formula.id"
              >
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                  <Link
                    :href="route('maintenance.formulas.edit', { formula })"
                    class="hover:text-gray-900"
                  >
                    {{ formula.name }}
                  </Link>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formula.defaults_for_humans }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
