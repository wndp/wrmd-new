<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import DeleteLocationModal from './DeleteLocationModal.vue';
import SaveLocationModal from './SaveLocationModal.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import { TrashIcon, PencilIcon, PrinterIcon } from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const props = defineProps({
  patientId: {
    type: String,
    required: true
  },
  locations: {
    type: Array,
    required: true
  },
  show: Boolean,
});

const emit = defineEmits(['close']);

const crudLocation = ref({});
const confirmingLocationDeletion = ref(false);
const showUpdateLocationModel = ref(false);

const close = () => emit('close');

const showConfirmingLocationDeletion = (location) => {
  crudLocation.value = location;
  confirmingLocationDeletion.value = true;
};

const showUpdateLocation = (location) => {
  crudLocation.value = location;
  showUpdateLocationModel.value = true;
};

const printMedicalRecord = (location) => {
    useForm({}).post(route('share.print.store', {
        admission: props.patientId,
        locationId: location.id,
        shareOptions: ['homecare']
    }), {
        preserveScroll: true,
        onStart: () => close()
    });
};
</script>

<template>
  <DialogModal
    :show="show"
    maxWidth="2xl"
    @close="close"
  >
    <template #title>
      {{ __('Location History') }}
    </template>
    <template #content>
      <div
        id="location_history"
        class="flex flex-col"
      >
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-hidden">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-100">
                  <tr>
                    <th
                      scope="col"
                      class="px-3 py-3"
                    />
                    <th
                      scope="col"
                      class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                    >
                      {{ __('Date Moved') }}
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                    >
                      {{ __('Facility') }}
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                    >
                      {{ __('Location') }}
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="location in locations"
                    :key="location.patient_location_id"
                  >
                    <td class="px-3 py-3 whitespace-nowrap text-right text-sm">
                      <div class="flex gap-2 text-blue-600">
                        <button
                          v-if="can(Abilities.MANAGE_LOCATIONS)"
                          dusk="delete-location"
                          @click="showConfirmingLocationDeletion(location)"
                        >
                          <TrashIcon class="w-5 h-5 text-red-600" />
                        </button>
                        <button
                          v-if="can(Abilities.MANAGE_LOCATIONS)"
                          dusk="edit-location"
                          @click="showUpdateLocation(location)"
                        >
                          <PencilIcon class="w-5 h-5" />
                        </button>
                        <button
                          v-if="can(Abilities.SHARE_PATIENTS) && location.facility_id === $page.props.locationOptionUiBehaviorIds.homecareFacilityId"
                          type="button"
                          @click="printMedicalRecord(location)"
                        >
                          <PrinterIcon class="w-5 h-5" />
                        </button>
                      </div>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ location.moved_in_at_for_humans }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                      {{ location.facility }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                      {{ location.location_for_humans }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        {{ __('Close') }}
      </SecondaryButton>
    </template>
  </DialogModal>
  <DeleteLocationModal
    v-if="confirmingLocationDeletion"
    :patientId="patientId"
    :location="crudLocation"
    :show="true"
    @close="confirmingLocationDeletion = false"
  />
  <SaveLocationModal
    v-if="showUpdateLocationModel"
    :patientId="patientId"
    :location="crudLocation"
    :show="true"
    title="Update Location"
    @close="showUpdateLocationModel = false"
  />
</template>
