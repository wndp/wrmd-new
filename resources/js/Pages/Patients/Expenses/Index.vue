<template>
  <PatientLayout title="Expenses">
    <div class="flex flex-col">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-blue-100">
                <tr class="divide-x divide-gray-200">
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    style="width: 100px"
                  />
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    style="width: 140px"
                  >
                    {{ __('Date') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Category / Memo') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    style="width: 100px"
                  >
                    {{ __('Debit') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    style="width: 100px"
                  >
                    {{ __('Credit') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  is="vue:TransactionRow"
                  v-for="transaction in transactions"
                  :key="transaction.id"
                  :transaction="transaction"
                />
                <tr>
                  <td
                    colspan="3"
                    class="pl-4 pr-3 py-4 text-right text-sm font-medium text-gray-900 sm:pl-0"
                  >
                    {{ __('Sub Totals') }}
                  </td>
                  <td class="pl-6 pr-3 py-4 text-left text-sm font-medium text-gray-900">
                    ${{ expenseTotals.totalDebits }}
                  </td>
                  <td class="pl-3 pr-6 py-4 text-right whitespace-nowrap text-sm font-medium text-gray-900">
                    ${{ expenseTotals.totalCredits }}
                  </td>
                </tr>
                <tr
                  v-if="can(Abilities.MANAGE_EXPENSES) && patient.locked_at === null"
                  class="divide-x divide-gray-200"
                >
                  <td
                    colspan="2"
                    class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 border-t-4 border-gray-200"
                  >
                    <DatePicker
                      id="transacted_at"
                      v-model="form.transacted_at"
                    />
                  </td>
                  <td class="px-1 py-1 whitespace-nowrap text-sm text-gray-500 border-t-4 border-gray-200">
                    <div class="flex items-center">
                      <Label
                        for="expense_category"
                        class="mr-4"
                      >
                        {{ __('Category') }}
                      </Label>
                      <Select
                        id="expense_category"
                        v-model="form.category"
                        name="category"
                        :options="categories"
                        class="border-transparent py-2 px-2 w-full"
                      />
                    </div>
                  </td>
                  <td class="px-1 py-1 whitespace-nowrap text-sm text-gray-500 text-center border-t-4 border-gray-200">
                    <Input
                      v-model="form.debit"
                      name="debit"
                      placeholder="Debit"
                      type="number"
                      min="0"
                      step="0.01"
                      class="text-center border-transparent py-2 px-2"
                    />
                  </td>
                  <td class="px-1 py-1 whitespace-nowrap text-sm text-gray-500 text-center border-t-4 border-gray-200">
                    <Input
                      v-model="form.credit"
                      name="credit"
                      placeholder="Credit"
                      type="number"
                      min="0"
                      step="0.01"
                      class="text-center border-transparent py-2 px-2"
                    />
                  </td>
                </tr>
                <tr
                  v-if="can(Abilities.MANAGE_EXPENSES) && patient.locked_at === null"
                  class="divide-x divide-gray-200"
                >
                  <td
                    colspan="3"
                    class="px-1 py-1 whitespace-nowrap text-sm"
                  >
                    <Input
                      v-model="form.memo"
                      name="memo"
                      placeholder="Memo"
                      class="border-transparent py-2 px-2"
                    />
                  </td>
                  <td
                    colspan="2"
                    class="px-1 py-1 whitespace-nowrap text-sm"
                  >
                    <PrimaryButton
                      class="items-center"
                      @click="store"
                    >
                      <PlusIcon class="w-5 h-5 mr-2" />
                      {{ __('Save') }}
                    </PrimaryButton>
                  </td>
                </tr>
                <tr class="divide-x divide-gray-200">
                  <td
                    v-if="form.hasErrors"
                    colspan="5"
                    class="px-1 py-1 whitespace-nowrap text-sm"
                  >
                    <ValidationErrors />
                  </td>
                </tr>
              </tbody>
              <tfoot class="bg-white">
                <tr>
                  <td
                    colspan="3"
                    class="hidden pl-4 pr-3 py-4 text-right text-sm font-medium text-gray-900 sm:table-cell sm:pl-0"
                  >
                    {{ __('Cost of Care 1') }}
                  </td>
                  <td
                    colspan="3"
                    class="pl-6 pr-3 py-4 text-left text-sm font-medium text-gray-900 sm:hidden"
                  >
                    {{ __('Cost of Care 2') }}
                  </td>
                  <td
                    colspan="2"
                    class="pl-3 pr-6 py-4 text-right whitespace-nowrap text-sm font-medium text-gray-900"
                  >
                    ${{ expenseTotals.costOfCare }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </PatientLayout>
</template>

<script setup>
import { inject, computed } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import ValidationErrors from '@/Components/FormElements/ValidationErrors.vue';
import TransactionRow from './Partials/TransactionRow.vue';
import { PlusIcon } from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const route = inject('route');

defineProps({
  transactions: {
    type: Array,
    required: true
  },
  expenseTotals: {
    type: Object,
    required: true
  }
})

let patient = computed(() => usePage().props.admission.patient);
let categories = computed(() => usePage().props.options.categories);

let form = useForm({
  transacted_at: null,
  category: null,
  debit: null,
  credit: null,
  memo: null
});

const store = () => {
  form.post(route('patients.expenses.store', {
    patient: patient.value.id
  }), {
    onSuccess: () => form.reset()
  });
};
</script>
