<script setup>
import {ref, computed} from 'vue';
import {usePage, router} from '@inertiajs/vue3';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { ChevronDownIcon } from '@heroicons/vue/24/outline';
import DailyTasksMenu from '@/Layouts/Partials/DailyTasksMenu.vue';
import PrintPatientModal from '@/Pages/Patients/Partials/PrintPatientModal.vue';
import ExportPatientModal from '@/Pages/Patients/Partials/ExportPatientModal.vue';
import EmailPatientModal from '@/Pages/Patients/Partials/EmailPatientModal.vue';
import {
  PrinterIcon, ArrowDownTrayIcon, AtSymbolIcon, IdentificationIcon,
  ClipboardDocumentListIcon, BeakerIcon, PhotoIcon, SwatchIcon, DevicePhoneMobileIcon, ScissorsIcon
} from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';
import axios from 'axios';

const isCollectedAlive = usePage().props.admission.patient?.event_processing.collection_condition === 'Alive';

const showPrintModal = ref(false);
const showExportModal = ref(false);
const showEmailModal = ref(false);
const currentRoute = ref(route().current());

const tabs = ref([
  isCollectedAlive ? { name: __('Field Stabilization'), route: 'oiled.field.edit' } : false,
  { name: __('Processing'), route: 'oiled.processing.edit' },
  isCollectedAlive ? { name: __('Intake'), route: 'oiled.intake.edit' } : false,
  isCollectedAlive ? { name: __('Wash'), route: 'oiled.wash.index' } : false,
  isCollectedAlive ? { name: __('Conditioning'), route: 'oiled.conditioning.index' } : false,
].filter(Boolean));

const dailyTasks = ref([]);

const more = ref([
  [
    { name: __('Case Summary'), icon: IdentificationIcon, action: 'oiled.summary.edit' },
    { name: __('Daily Exams'), icon: ClipboardDocumentListIcon, action: 'patients.exam.index' },
    { name: __('Labs'), icon: BeakerIcon, action: 'patients.lab.index' },
    { name: __('Attachments'), icon: PhotoIcon, action: 'patients.attachments.edit' },
    { name: __('Banding & Morphometrics'), icon: SwatchIcon, action: 'patients.research.edit' },
    { name: __('Necropsy'), icon: ScissorsIcon, action: 'patients.necropsy.edit' },
    { name: __('Wildlife Recovery Data'), icon: DevicePhoneMobileIcon, action: 'owcn.wrapp.index' },
  ],
  [
    { name: __('Print Cage Card Label'), icon: PrinterIcon, action: 'print' },
    { name: __('Print Evidence Label'), icon: PrinterIcon, action: 'print' },
  ],
  [
    { name: __('Print Patient Medical Record'), icon: PrinterIcon, action: 'print' },
    { name: __('Export Patient to Spreadsheet'), icon: ArrowDownTrayIcon, action: 'export' },
    { name: __('Email Patient Medical Record'), icon: AtSymbolIcon, action: 'email' },
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
      let accountAddress = usePage().props.auth.account.full_address;
      let foundAddress = admission.value.patient.location_found;
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
};
</script>

<template>
  <div class="mt-8">
    <div class="sm:hidden">
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
        <optgroup label="More">
          <option
            v-for="item in more.flat()"
            :key="item.name"
            :value="item.action"
          >
            {{ item.name }}
          </option>
        </optgroup>
      </select>
    </div>
    <div class="hidden sm:block border-b border-gray-200 pb-1">
      <nav
        class="flex space-x-1 md:space-x-2 lg:space-x-4"
        aria-label="Tabs"
      >
        <Link
          v-for="tab in tabs"
          :key="tab.name"
          :href="route(tab.route, caseQueryString)"
          :class="[tab.route === currentRoute ? 'bg-blue-300 text-gray-800' : 'text-gray-600 hover:text-gray-900', 'px-3 py-2 font-medium text-sm rounded-md whitespace-nowrap']"
          :aria-current="tab.route === currentRoute ? 'page' : undefined"
        >
          {{ tab.name }}
        </Link>
        <DailyTasksMenu @ready="dailyTasks = $event.tabGroups" />
        <Menu
          as="div"
          class="relative"
        >
          <MenuButton class="relative inline-flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 font-medium text-sm rounded-md whitespace-nowrap">
            {{ __('More') }}
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
            <MenuItems class="origin-top-right absolute right-0 z-40 mt-2 -mr-1 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none divide-y-4 divide-gray-100">
              <div
                v-for="(items, i) in more"
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
                      :is="item.icon"
                      class="h-4 w-4 mr-2"
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
