<script setup>
//import UnrecognizedMixin from './UnrecognizedMixin.js';
import {ref} from 'vue';
///import UnrecognizedPatientsRow from './UnrecognizedPatientsRow.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  admissions: {
    type: Array,
    required: true
  }
});

const mutableAdmissions = ref(props.admissions)

</script>

<template>
  <div>
    <div
      v-if="false"
      class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200"
    >
     mutableAdmissions.length === 0
      <div
        v-if="heading"
        class="px-4 py-5 sm:px-6"
      >
        {{ heading }}
      </div>
      <div class="px-4 py-5 sm:p-6 prose max-w-none">
        <h6>{{ __('There are no unrecognized patients.') }}</h6>
      </div>
    </div>
    <div
      v-else
      class="flex flex-col"
    >
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <caption v-if="heading">
                {{ heading }}
              </caption>
              <thead class="bg-blue-100">
                <tr>
                  <th
                    v-if="! hasAccount"
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Account
                  </th>
                  <th
                    v-if="! hasAccount"
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Location
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    {{ __('Case Number') }}
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    {{ __('Current Common Name') }}
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                    style="width: 300px"
                  >
                    {{ __('New Common Name') }}
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  />
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <!-- <UnrecognizedPatientsRow
                  v-for="(admission, index) in mutableAdmissions"
                  :key="admission.id+admission.patient_id"
                  :row="admission"
                  :index="index"
                  @updated="deleteRow"
                  @updated-all="deleteRows"
                /> -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
