<script setup>
import {ref, onMounted} from 'vue';
import {usePage} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Paginator from '@/Components/Paginator.vue';
import MaintenanceAside from './Partials/MaintenanceAside.vue';
import TransferModal from './Partials/TransferModal.vue';
import Badge from '@/Components/Badge.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import isUndefined from 'lodash/isUndefined';
import {__} from '@/Composables/Translate';

const props = defineProps({
  transfers: {
    type: Object,
    required: true
  },
  unansweredTransfers: {
    type: Array,
    required: true
  },
  uuid: {
    type: String,
    default: null
  }
});

const transferDetails = ref({});
const showTransferModal = ref(false);

onMounted(() => {
  if (props.uuid !== null) {
    transferDetails.value = props.unansweredTransfers.find(t => t.uuid === props.uuid);

    if (isUndefined(transferDetails.value)) return;

    showTransferModal.value = true;
  }
});

const admissionFrom = (transfer) => {
  return transfer.patient.admissions.find(admission =>
    admission.team_id === transfer.from_team_id
  );
};

const admissionTo = (transfer) => {
  let patient = transfer.is_collaborative ? transfer.patient : transfer.cloned_patient;

  return patient.admissions.find(admission =>
    admission.team_id === transfer.to_team_id
  );
};

const getStatus = (transfer) => {
  if (transfer.responded_at === null) return __('No Response');
  return transfer.is_accepted ? __('Accepted') : __('Denied');
};

const getStatusColor = (transfer) => {
  if (transfer.responded_at === null) return 'orange';
  return transfer.is_accepted ? 'green' : 'red';
};

const isTransferToAuthTeam = (transfer) => {
  return transfer.to_team_id === usePage().props.auth.team.id;
};

const showTransfer = (transfer) => {
  transferDetails.value = transfer;
  showTransferModal.value = true;
};
</script>

<template>
  <AppLayout title="Maintenance">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <MaintenanceAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
          <div class="bg-white py-6 px-4 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ __('Patient Transfers') }}
            </h3>
            <p class="mt-1 text-sm text-gray-500">
              Lorem, ipsum dolor sit amet consectetur adipisicing elit. Alias quasi non dolor pariatur tenetur aut assumenda culpa magni optio? Eveniet, facere? Eligendi quo numquam soluta dolorum expedita rerum dolore corporis?
            </p>
          </div>
        </div>
        <div
          v-if="unansweredTransfers.length > 0"
          class="flex flex-col mt-8"
        >
          <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
              <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                  <thead class="bg-blue-100">
                    <tr>
                      <th
                        scope="col"
                        class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                      >
                        {{ __('Unanswered Transfer Requests') }}
                      </th>
                      <th
                        scope="col"
                        class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                      >
                        {{ __('Patient') }}
                      </th>
                      <th
                        scope="col"
                        class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                      >
                        {{ __('Date Sent') }}
                      </th>
                      <th
                        scope="col"
                        class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                      />
                      <th
                        scope="col"
                        class="relative py-3.5 pl-3 pr-4 sm:pr-6"
                      >
                        <span class="sr-only">Details</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 bg-white">
                    <tr
                      v-for="transfer in unansweredTransfers"
                      :key="transfer.id"
                    >
                      <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                        <div class="flex items-center">
                          <div class="h-10 w-10 flex-shrink-0">
                            <img
                              class="h-10 w-10 rounded-full"
                              :src="transfer.from_team.profile_photo_url"
                              alt=""
                            >
                          </div>
                          <div class="ml-4">
                            <div class="font-medium text-gray-900">
                              <span class="text-blue-700">{{ __('From') }}:</span> {{ transfer.from_team.name }}
                            </div>
                            <div class="text-gray-500">
                              {{ transfer.from_team.locale }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        <div class="text-gray-900">
                          {{ transfer.patient.common_name }}
                        </div>
                      </td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        <div class="text-gray-900">
                          {{ transfer.created_at_for_humans }}
                        </div>
                      </td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        <Badge
                          v-if="transfer.is_collaborative"
                          color="blue"
                        >
                          {{ __('Collaborative') }}
                        </Badge>
                      </td>
                      <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <PrimaryButton @click="showTransfer(transfer)">
                          Answer This Request
                        </PrimaryButton>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="flex flex-col mt-8">
          <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
              <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                  <thead class="bg-blue-100">
                    <tr>
                      <th
                        scope="col"
                        class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                      >
                        {{ __('Transferred With') }}
                      </th>
                      <th
                        scope="col"
                        class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                      >
                        {{ __('Case Numbers') }}
                      </th>
                      <th
                        scope="col"
                        class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                      >
                        {{ __('Common Name') }}
                      </th>
                      <th
                        scope="col"
                        class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                      >
                        {{ __('Status') }}
                      </th>
                      <th
                        scope="col"
                        class="relative py-3.5 pl-3 pr-4 sm:pr-6"
                      >
                        <span class="sr-only">{{ __('Details') }}</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 bg-white">
                    <tr
                      v-for="transfer in transfers.data"
                      :key="transfer.id"
                    >
                      <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                        <div class="flex items-center">
                          <div class="h-10 w-10 flex-shrink-0">
                            <img
                              class="h-10 w-10 rounded-full"
                              :src="isTransferToAuthTeam(transfer) ? transfer.from_team.profile_photo_url : transfer.to_team.profile_photo_url"
                              alt=""
                            >
                          </div>
                          <div class="ml-4">
                            <div class="font-medium text-gray-900">
                              <span class="text-blue-700">{{ __('From') }}:</span> {{ transfer.from_team.name }}
                            </div>
                            <div class="text-gray-900">
                              <span class="text-green-700">{{ __('To') }}:</span> {{ transfer.to_team.name }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm align-top text-gray-500">
                        <div class="text-gray-900">
                          {{ admissionFrom(transfer).case_number }}
                        </div>
                        <div
                          v-if="transfer.responded_at !== null && transfer.is_accepted"
                          class="text-gray-500"
                        >
                          {{ admissionTo(transfer)?.case_number }}
                          <!-- <Link
                        v-if="transfer.is_accepted"
                        :href="route('patients.continued.edit', {c: transfer.admission.id, y: transfer.admission.case_year})"
                      >
                        {{ transfer.admission.case_number }}
                      </Link> -->
                        </div>
                      </td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm align-top text-gray-900">
                        <div>{{ transfer.patient.common_name }}</div>
                        <Badge
                          v-if="transfer.is_collaborative"
                          color="blue"
                        >
                          {{ __('Collaborative') }}
                        </Badge>
                      </td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        <button
                          v-if="isTransferToAuthTeam(transfer) && transfer.responded_at === null"
                          class="ml-3 text-blue-600 hover:text-blue-900"
                          @click="showTransfer(transfer)"
                        >
                          {{ __('Answer') }}
                        </button>
                        <div
                          v-else
                          class="text-gray-500 mt-1"
                        >
                          {{ transfer.responded_at_formatted }}
                        </div>
                        <Badge :color="getStatusColor(transfer)">
                          {{ getStatus(transfer) }}
                        </Badge>
                      </td>
                      <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <button
                          class="block text-blue-600 hover:text-blue-900"
                          @click="showTransfer(transfer)"
                        >
                          {{ __('Details') }}
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <Paginator
          :properties="transfers"
          class="mt-8"
        />
      </div>
    </div>
    <TransferModal
      :transfer="transferDetails"
      :show="showTransferModal"
      @close="showTransferModal = false"
    />
  </AppLayout>
</template>
