<script setup>
import {ref} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import IncidentHeader from './Partials/IncidentHeader.vue';
import IncidentTabs from './Partials/IncidentTabs.vue';
import SaveCommunicationModal from './Partials/SaveCommunicationModal.vue';
import DeleteCommunicationModal from './Partials/DeleteCommunicationModal.vue';
import { TrashIcon, PencilIcon } from '@heroicons/vue/24/outline';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

defineProps({
  incident: {
    type: Object,
    required: true
  },
  communications: {
    type: Array,
    required: true
  },
  statusOpenId: {
    type: Number,
    required: true
  },
  statusUnresolvedId: {
    type: Number,
    required: true
  },
  statusResolvedId: {
    type: Number,
    required: true
  }
});

const showUpsertCommunicationModel = ref(false);
const confirmingDeletionModel = ref(false);
const crudCommunication = ref({});

const showConfirmingCommunicationDeletion = (communication) => {
  crudCommunication.value = communication;
  confirmingDeletionModel.value = true;
};

const showUpsertCommunication = (communication = {}) => {
  crudCommunication.value = communication;
  showUpsertCommunicationModel.value = true;
};
</script>

<template>
  <AppLayout title="Hotline">
    <IncidentHeader
      :incident="incident"
      :statusOpenId="statusOpenId"
      :statusUnresolvedId="statusUnresolvedId"
      :statusResolvedId="statusResolvedId"
    />
    <IncidentTabs
      :incident="incident"
      class="mt-4"
    />
    <div class="flex flex-col mt-8">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
              <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
                <div class="ml-4 mt-4">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ __('Communications') }}
                  </h3>
                </div>
                <div class="ml-4 mt-4 flex-shrink-0">
                  <PrimaryButton @click="showUpsertCommunication()">
                    {{ __('Create New Communication') }}
                  </PrimaryButton>
                </div>
              </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    style="width: 50px"
                  />
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Date') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('By') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Comments') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="communication in communications"
                  :key="communication.id"
                >
                  <td class="px-3 py-3 whitespace-nowrap text-sm">
                    <div class="flex justify-between items-start">
                      <button dusk="communication-delete">
                        <TrashIcon
                          class="w-5 h-5 mr-4 text-red-600"
                          @click="showConfirmingCommunicationDeletion(communication)"
                        />
                      </button>
                      <button dusk="communication-edit">
                        <PencilIcon
                          class="w-5 h-5 mr-4 text-blue-600"
                          @click="showUpsertCommunication(communication)"
                        />
                      </button>
                    </div>
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ communication.communication_at_for_humans }}
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                    {{ communication.communication_by }}
                  </td>
                  <td class="px-3 py-3 text-sm text-gray-500">
                    {{ communication.communication }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <DeleteCommunicationModal
      v-if="confirmingDeletionModel"
      :incident="incident"
      :communication="crudCommunication"
      :show="true"
      @close="confirmingDeletionModel = false"
    />
    <SaveCommunicationModal
      v-if="showUpsertCommunicationModel"
      :incident="incident"
      :communication="crudCommunication"
      :show="true"
      @close="showUpsertCommunicationModel = false"
    />
  </AppLayout>
</template>
