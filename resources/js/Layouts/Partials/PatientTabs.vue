<script setup>
import {ref, computed} from 'vue';
import {usePage, router} from '@inertiajs/vue3';
import MorePatientPagesMenu from './MorePatientPagesMenu.vue';
import DailyTasksMenu from './DailyTasksMenu.vue';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { ChevronDownIcon } from '@heroicons/vue/24/outline';
import PrintPatientModal from '@/Pages/Patients/Partials/PrintPatientModal.vue';
import ExportPatientModal from '@/Pages/Patients/Partials/ExportPatientModal.vue';
import EmailPatientModal from '@/Pages/Patients/Partials/EmailPatientModal.vue';
import * as heroIcons from "@heroicons/vue/24/outline";
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
import axios from 'axios';
import ExtensionApi from '@/Composables/ExtensionApi';

const extension = ExtensionApi();

//const heroIcons = heroIcons;

const showPrintModal = ref(false);
const showExportModal = ref(false);
const showEmailModal = ref(false);
const currentRoute = ref(route().current());

const tabs = ref([
  can(Abilities.COMPUTED_VIEW_RESCUER) ? { name: __('Rescuer'), route: 'patients.rescuer.edit' } : false,
  { name: __('Initial Care'), route: 'patients.initial.edit' },
  { name: __('Continued Care'), route: 'patients.continued.edit' },
].filter(Boolean));

const dailyTasks = ref([]);
const more = ref([]);

const share = ref([
  can(Abilities.SHARE_PATIENTS) ? [
    { name: __('Print Patient Medical Record'), icon: 'PrinterIcon', action: 'print' },
    { name: __('Export Patient to Spreadsheet'), icon: 'ArrowDownTrayIcon', action: 'export' },
    { name: __('Email Patient Medical Record'), icon: 'AtSymbolIcon', action: 'email' },
  ].concat(extension.navigation('patient.tabs.share')) : [],
  [extension.navigation('patient.tabs.share.group').flat(1)].flat(),
  [
    can(Abilities.COMPUTED_VIEW_TRANSFER_PATIENT) ? { name: __('Transfer Patient'), icon: 'ShareIcon', action: 'share.transfer.create' } : false
  ].filter(Boolean),
  [
    { name: __('Directions to Address Found'), icon: 'TruckIcon', action: 'directions' },
  ]
].filter(array => array.length));

const caseQueryString = ref({
  y: usePage().props.admission.case_year,
  c: usePage().props.admission.case_id,
});

const admission = computed(() => usePage().props.admission);

const handleSelectedItemChange = () => handleMore(currentRoute.value);

const handleMore = (action) => {
  const handleMore = {
    print: () => showPrintModal.value = true,
    export: () => showExportModal.value = true,
    email: () => showEmailModal.value = true,
    directions: () => {
      let accountAddress = usePage().props.auth.team.formatted_inline_address;
      let foundAddress = `${admission.value.patient.address_found} ${admission.value.patient.city_found} ${admission.value.patient.subdivision_found}`;

      window.open(`http://maps.google.com/maps?saddr=${ accountAddress }&daddr=${ foundAddress }`, '_blank').focus();
    }
  };

  if (Object.hasOwn(handleMore, action)) {
    handleMore[action]();
  } else if (route().has(action)) {
    router.visit(route(action, caseQueryString.value));
  } else {
    // This is very specific to generating a report
    axios.post(action)
  }
}
</script>

<template>
  <div class="mt-8">
    <div class="lg:hidden">
      <label
        for="tabs"
        class="sr-only"
      >{{ __('Select a tab') }}</label>
      <select
        id="tabs"
        v-model="currentRoute"
        name="tabs"
        class="block w-full focus:ring-blue-500 focus:border-blue-500 border-gray-300 rounded-md"
        @change="handleSelectedItemChange"
      >
        <option
          v-for="tab in tabs"
          :key="tab.name"
          :value="tab.route"
        >
          {{ tab.name }}
        </option>
        <optgroup :label="__('Daily Tasks')">
          <option
            v-for="item in dailyTasks.flat()"
            :key="item.name"
            :value="item.action"
          >
            {{ item.name }}
          </option>
        </optgroup>
        <optgroup :label="__('More')">
          <option
            v-for="item in more.flat()"
            :key="item.name"
            :value="item.route"
          >
            {{ item.name }}
          </option>
        </optgroup>
        <optgroup :label="__('Sharing')">
          <option
            v-for="item in share.flat()"
            :key="item.name"
            :value="item.action"
          >
            {{ item.name }}
          </option>
        </optgroup>
      </select>
    </div>
    <div class="hidden lg:block border- border-gray-20 pb-1">
      <nav
        class="flex space-x-1 md:space-x-2 lg:space-x-4"
        aria-label="Tabs"
      >
        <Link
          v-for="tab in tabs"
          :key="tab.name"
          :href="route(tab.route, caseQueryString)"
          :class="[tab.route === currentRoute ? 'bg-blue-400 text-gray-800 rounded-md' : 'border-transparent text-gray-600 hover:border-blue-400 hover:text-gray-900', 'px-3 py-2 font-bold text-sm whitespace-nowrap border-b-2']"
          :aria-current="tab.route === currentRoute ? 'page' : undefined"
        >
          {{ tab.name }}
        </Link>
        <DailyTasksMenu @ready="dailyTasks = $event.tabGroups" />
        <MorePatientPagesMenu @ready="more = $event.tabGroups" />
        <Menu
          as="div"
          class="relative"
        >
          <MenuButton class="relative inline-flex items-center px-3 py-2 border-b-2 border-transparent text-gray-600 hover:border-blue-400 hover:text-gray-900 font-medium text-sm whitespace-nowrap">
            {{ __('Sharing') }}
            <ChevronDownIcon
              class="h-5 w-5 flex-shrink-0 ml-1"
              aria-hidden="true"
            />
          </MenuButton>
          <transition
            enterActiveClass="transition ease-out duration-100"
            enterFromClass="transform opacity-0 scale-95"
            enterToClass="transform opacity-100 scale-100"
            leaveActiveClass="transition ease-in duration-75"
            leaveFromClass="transform opacity-100 scale-100"
            leaveToClass="transform opacity-0 scale-95"
          >
            <MenuItems class="origin-top-right absolute right-0 z-10 mt-2 -mr-1 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none divide-y-4 divide-gray-100">
              <div
                v-for="(items, i) in share"
                :key="i"
              >
                <MenuItem
                  v-for="item in items"
                  :key="item.name"
                  v-slot="{ active }"
                >
                  <button
                    :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex items-center w-full px-4 py-2 text-sm']"
                    @click="handleMore(item.action)"
                  >
                    <Component
                      :is="heroIcons[item.icon]"
                      class="h-5 w-5 mr-2"
                    />
                    {{ item.name }}
                  </button>
                </MenuItem>
              </div>
            </MenuItems>
          </transition>
        </Menu>
      </nav>
    </div>
  </div>
  <PrintPatientModal
    :patient="admission.patient"
    :show="showPrintModal"
    @close="showPrintModal = false"
  />
  <ExportPatientModal
    :patient="admission.patient"
    :show="showExportModal"
    @close="showExportModal = false"
  />
  <EmailPatientModal
    :patient="admission.patient"
    :show="showEmailModal"
    @close="showEmailModal = false"
  />
</template>
