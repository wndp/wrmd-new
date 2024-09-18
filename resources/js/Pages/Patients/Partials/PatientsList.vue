<script setup>
import {ref, computed, onMounted} from 'vue';
import {useForm} from '@inertiajs/vue3';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import PatientActions from './PatientActions.vue';
import intersection from 'lodash/intersection';
import {__} from '@/Composables/Translate';

const props = defineProps({
  data: Object,
  hasQueryCache: {
      type: Boolean,
      default: false
  }
});

const patientIds = ref(props.data.selected);
const patientsSelected = ref(false);

const showActions = computed(() => props.hasQueryCache || patientIds.value.length > 0);

const allPatientsSelected = computed(() => {
  let count = props.data.rows.total;

  return count !== 0 && intersection(
      patientIds.value,
      props.data.rows.data.map(p => p.patient_id)
  ).length === count;
});

const indeterminate = computed(() => patientIds.value.length > 0 && patientIds.value.length < props.data.rows.total);

onMounted(() => patientsSelected.value = allPatientsSelected.value);

const onAllPatientsChanged = (e) => {
  let allPatients = props.data.rows.data.map(p => p.patient_id);
  if (e.target.checked) {
      useForm({
        patients: allPatients
      }).post(route('select-patients.store'), {
          preserveState: false
      });
  } else {
      useForm({
        patients: allPatients
      }).delete(route('select-patients.store'), {
          preserveState: false
      });
  }
};

const formatNumber = (number) => new Intl.NumberFormat().format(number);

const togglePatient = (patientId) => {
  if (patientIds.value.includes(patientId)) {
      useForm({}).post(route('select-patient.store', {
          patient: patientId
      }));
  } else {
      useForm({}).delete(route('select-patient.destroy', {
          patient: patientId
      }));
  }
}
</script>

<template>
  <div class="flex relative flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
          <div class="bg-white py-4 px-4">
            <div
              v-if="showActions"
              class="flex text-sm text-gray-600"
            >
              <strong class="font-bold mr-1">{{ __('Selected Patients') }}</strong>
              {{ formatNumber(patientIds.length) }}
              <PatientActions class="ml-8" />
            </div>
            <div
              v-else
              class="text-sm text-gray-600"
            >
              <strong class="font-bold mr-1">{{ __('Total Patients') }}</strong>
              {{ formatNumber(data.rows.total) }}
            </div>
          </div>
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-blue-100">
              <tr>
                <th
                  scope="col"
                  class="px-2 py-2 whitespace-nowrap text-right text-sm"
                  style="width: 40px;"
                >
                  <Checkbox
                    :checked="indeterminate || allPatientsSelected"
                    :indeterminate="indeterminate"
                    name="patients"
                    @change="onAllPatientsChanged"
                  />
                </th>
                <th
                  scope="col"
                  class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider align-bottom"
                >
                  {{ __('Case Number') }}
                </th>
                <th
                  scope="col"
                  class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider align-bottom"
                >
                  {{ __('Common Name') }}
                </th>
                <th
                  v-for="header in data.headers"
                  :key="header.key"
                  scope="col"
                  class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider align-bottom"
                >
                  {{ header.label }}
                </th>
                <th
                  scope="col"
                  class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider align-bottom"
                >
                  {{ __('Date Admitted') }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="patient in data.rows.data"
                :key="patient.patient_id"
                :class="[patientIds.includes(patient.patient_id) && 'bg-gray-50']"
              >
                <td class="px-2 py-2 whitespace-nowrap text-right text-sm relative">
                  <div
                    v-if="patientIds.includes(patient.patient_id)"
                    class="absolute inset-y-0 left-0 w-0.5 bg-green-600"
                  />
                  <Checkbox
                    v-model="patientIds"
                    name="patient"
                    :value="patient.patient_id"
                    @change="togglePatient(patient.patient_id)"
                  />
                </td>
                <td class="px-2 py-2 whitespace-nowrap text-sm font-medium text-blue-700">
                  <Link :href="patient.link">
                    {{ patient.case_number }}
                  </Link>
                </td>
                <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                  {{ patient.common_name }}
                </td>
                <td
                  v-for="header in data.headers"
                  :key="header.key"
                  class="px-2 py-2 text-sm text-gray-500"
                >
                  {{ patient[header.key] }}
                </td>
                <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                  {{ patient.admitted_at }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
