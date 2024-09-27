<script setup>
import {ref, computed, onMounted, onBeforeUnmount} from 'vue';
import {usePage} from '@inertiajs/vue3';
import AppLayout from './AppLayout.vue';
import CageCardModal from './Partials/CageCardModal.vue';
import SaveLocationModal from './Partials/SaveLocationModal.vue';
import LocationHistoryModal from './Partials/LocationHistoryModal.vue';
import PatientPagination from './Partials/PatientPagination.vue';
import PatientMetaTags from './Partials/PatientMetaTags.vue';
import PatientNotifications from './Partials/PatientNotifications.vue';
import PatientTabs from './Partials/PatientTabs.vue';
import OiledPatientTabs from './Partials/OiledPatientTabs.vue';
import { TransitionRoot } from '@headlessui/vue';
import { PencilIcon, MapPinIcon, ClockIcon } from '@heroicons/vue/24/outline';
import debounce from 'lodash/debounce';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

defineProps({
  title: {
    type: String,
    default: 'WRMD'
  }
});

const subject = ref(null);
const showCageCard = ref(false);
const showCreateNewLocation = ref(false);
const showHistory = ref(false);
const showStickyHeader = ref(false);
const handleDebouncedScroll = ref(null);
const appHeaderHeight = ref(null);

const currentLocation = computed(() => usePage().props.locationCard.patientLocations?.[0] || null);

const dispositionIsNotPending = computed(
  () => usePage().props.locationCard.disposition_id !== usePage().props.locationOptionUiBehaviorIds.dispositionPendingId
);

const showCurrentLocation = computed(
  () => ! [
    usePage().props.locationOptionUiBehaviorIds.dispositionReleasedId,
    usePage().props.locationOptionUiBehaviorIds.dispositionTransferredId
  ].includes(usePage().props.locationCard.disposition_id)
);

const tabsComponent = computed(() => {
  let isOwrmd = false;
  return isOwrmd ? OiledPatientTabs : PatientTabs;
  // return {
  //   OiledPatientTabs
  // }[this.extensionNavigation('patient.tabs_component')] || PatientTabs;
});

const customFields = [];
// const customFields = computed(() => {
//   return (usePage().props.extensions['patient.customFields'] ?? [])
//     .flat()
//     .filter(field => {
//         return field.group === 'Patient'
//             && field.panel === 'Cage Card'
//     })
// });

const handleScroll = () => {
  let tabsBottom = subject.value.getBoundingClientRect().bottom;
  showStickyHeader.value = tabsBottom < appHeaderHeight.value;
}

onMounted(() => {
  appHeaderHeight.value = document.querySelector('#app-header').getBoundingClientRect().height;
  handleDebouncedScroll.value = debounce(handleScroll, 20);
  document.addEventListener('scroll', handleDebouncedScroll.value, true);
});

onBeforeUnmount(() => document.removeEventListener('scroll', handleDebouncedScroll.value, true));

//export default {
  // computed: {
  //   tabsComponent() {
  //     return {
  //       OiledPatientTabs
  //     }[this.extensionNavigation('patient.tabs_component')] || PatientTabs
  //   },
  //   customFields() {
  //     return (this.$page.props.extensions['patient.customFields'] ?? [])
  //       .flat()
  //       .filter(field => {
  //           return field.group === 'Patient'
  //               && field.panel === 'Cage Card'
  //       });
  //   }
  // },
//};
</script>

<template>
  <AppLayout :title="title">
    <TransitionRoot
      as="template"
      :show="showStickyHeader"
      enter="transform transition ease-in-out duration-300"
      enterFrom="opacity-0"
      enterTo="opacity-100"
      leave="transform transition ease-in-out duration-75"
      leaveFrom="opacity-100"
      leaveTo="opacity-0"
    >
      <div class="z-20 sticky top-0 -mx-2 text-base md:text-xl leading-6 font-medium text-gray-700 opacity-95">
        <div class="py-4 px-4 flex justify-between bg-white border-b border-gray-200 shadow-md rounded-b-md">
          <h3>
            {{ $page.props.cageCard.case_number }} {{ $page.props.cageCard.common_name }} <template v-if="$page.props.cageCard.morph_id !== null">
              ({{ $page.props.cageCard.morph }})
            </template>
          </h3>
          <h3 v-if="currentLocation && !dispositionIsNotPending">
            {{ currentLocation.location_for_humans }}
          </h3>
          <h3
            v-else-if="dispositionIsNotPending"
            class="text-red-600"
          >
            {{ $page.props.locationCard.disposition }}
          </h3>
        </div>
      </div>
    </TransitionRoot>
    <PatientPagination />
    <PatientMetaTags :patientMeta="$page.props.patientMeta" />
    <div
      ref="subject"
      class="grid sm:grid-cols-2 gap-4 mt-4"
    >
      <div class="col-span-1 bg-white overflow-hidden shadow rounded-lg">
        <div class="h-full flex flex-col justify-between">
          <div class="px-4 py-2 border-b border-gray-200">
            <div class="text-base md:text-xl leading-6 font-medium text-gray-900 truncate">
              {{ $page.props.cageCard.case_number }} {{ $page.props.cageCard.common_name }} <template v-if="$page.props.cageCard.morph_id !== null">
                ({{ $page.props.cageCard.morph }})
              </template>
            </div>
          </div>
          <div class="px-4 py-2">
            <div class="md:grid grid-cols-2 gap-x-4 text-sm overflow-x-auto whitespace-nowrap space-y-2">
              <div class="col-span-2">
                <div class="text-md text-gray-700">
                  <strong class="font-normal mr-2">{{ __('Date Admitted') }}</strong>
                  <span class="font-medium">{{ $page.props.cageCard.admitted_at_for_humans }}</span>
                </div>
              </div>
              <div class="space-y-2">
                <div class="text-md text-gray-700">
                  <strong class="font-normal mr-2">{{ __('Band') }}</strong>
                  <span class="font-medium">{{ $page.props.cageCard.band }}</span>
                </div>
                <div class="text-md text-gray-700">
                  <strong class="font-normal mr-2">{{ __('Name') }}</strong>
                  <span class="font-medium">{{ $page.props.cageCard.name }}</span>
                </div>
              </div>
              <div class="space-y-2 mt-2 md:mt-0">
                <div class="text-md text-gray-700">
                  <strong class="font-normal mr-2">{{ __('Reference') }}</strong>
                  <span class="font-medium">{{ $page.props.cageCard.reference_number }}</span>
                </div>
                <div class="text-md text-gray-700">
                  <strong class="font-normal mr-2">{{ __('Microchip') }}</strong>
                  <span class="font-medium">{{ $page.props.cageCard.microchip_number }}</span>
                </div>
              </div>
              <div class="space-y-2 mt-2 md:mt-0">
                <div
                  v-for="customField in customFields"
                  :key="customField.id"
                  class="text-md text-gray-700"
                >
                  <strong class="font-normal mr-2">{{ customField.label }}</strong>
                  <span class="font-medium">{{ admission.patient.custom_values?.[`custom_field_${customField.account_field_id}`] }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="px-4 py-2">
            <button
              v-if="can(Abilities.UPDATE_CAGE_CARD) && $page.props.patientMeta.locked_at === null"
              type="button"
              class="flex items-center text-blue-600 text-sm"
              @click="showCageCard = true"
            >
              <PencilIcon class="h-4 w-4 mr-1" />
              <span>{{ __('Update Cage Card') }}</span>
            </button>
          </div>
        </div>
      </div>
      <div
        id="current_location"
        class="col-span-1 bg-white overflow-hidden shadow rounded-lg"
        style="min-height: 150px"
      >
        <div class="h-full flex flex-col justify-between">
          <div class="px-4 py-2 border-b border-gray-200">
            <div class="text-base md:text-xl leading-6 font-medium text-gray-900 truncate">
              {{ __('Location') }}
            </div>
          </div>
          <div class="px-4 py-2">
            <div
              v-if="currentLocation && showCurrentLocation"
              class="overflow-x-auto whitespace-nowrap"
            >
              <div class="text-base text-gray-700 font-medium">
                {{ currentLocation.location_for_humans }}
              </div>
              <div class="md:flex justify-between text-sm mt-2">
                <div class="text-md text-gray-700 mr-4">
                  <strong class="font-normal mr-2">{{ __('Facility') }}</strong>
                  <span class="font-medium">{{ currentLocation.facility }}</span>
                </div>
                <div class="text-md text-gray-700 mt-2 md:mt-0">
                  <strong class="font-normal mr-2">{{ __('Date Moved') }}</strong>
                  <span class="font-medium">{{ currentLocation.moved_in_at_for_humans }}</span>
                </div>
              </div>
            </div>
            <div
              v-else-if="dispositionIsNotPending"
              class="overflow-x-auto whitespace-nowrap"
            >
              <div class="text-base text-red-600 font-medium">
                {{ $page.props.locationCard.disposition }}
              </div>
              <div class="md:flex justify-between text-sm mt-2">
                <div class="text-md text-gray-700 mr-4">
                  <strong class="font-normal mr-2">Disposition Date</strong>
                  <span class="font-medium">{{ $page.props.locationCard.dispositioned_at_formatted }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="px-4 py-2 flex justify-between">
            <button
              v-if="can(Abilities.MANAGE_LOCATIONS) && $page.props.patientMeta.locked_at === null"
              type="button"
              class="flex items-center text-blue-600 text-sm"
              @click="showCreateNewLocation = true"
            >
              <MapPinIcon class="h-4 w-4 mr-1" />
              <span>{{ __('New Location') }}</span>
            </button>
            <button
              type="button"
              class="flex items-center text-blue-600 text-sm"
              @click="showHistory = true"
            >
              <ClockIcon class="h-4 w-4 mr-1" />
              <span>{{ __('History') }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <slot name="tabs">
      <component
        :is="tabsComponent"
        :patientId="$page.props.admission.patient_id"
      />
    </slot>
    <div
      id="content"
      class="mt-4"
    >
      <slot />
    </div>
    <CageCardModal
      :data="$page.props.cageCard"
      :show="showCageCard"
      @close="showCageCard = false"
    />
    <SaveLocationModal
      :patientId="$page.props.admission.patient_id"
      :show="showCreateNewLocation"
      title="Move to New Location"
      @close="showCreateNewLocation = false"
    />
    <LocationHistoryModal
      :patientId="$page.props.admission.patient_id"
      :locations="$page.props.locationCard.patientLocations"
      :show="showHistory"
      @close="showHistory = false"
    />
    <PatientNotifications :patientId="$page.props.admission.patient_id" />
  </AppLayout>
</template>
