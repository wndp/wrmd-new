<script setup>
import {ref, computed} from 'vue';
import {usePage, router} from '@inertiajs/vue3';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { ChevronDownIcon } from '@heroicons/vue/24/outline';
import SaveRecheckModal from '@/Pages/DailyTasks/Partials/SaveRecheckModal.vue';
import SavePrescriptionModal from '@/Pages/DailyTasks/Partials/SavePrescriptionModal.vue';
import SaveNutritionModal from '@/Pages/DailyTasks/Partials/SaveNutritionModal.vue';
import PrintPatientModal from '@/Pages/Patients/Partials/PrintPatientModal.vue';
import ExportPatientModal from '@/Pages/Patients/Partials/ExportPatientModal.vue';
import EmailPatientModal from '@/Pages/Patients/Partials/EmailPatientModal.vue';
import {
  CalendarIcon,
  BellAlertIcon,
  CalendarDaysIcon,
  ClockIcon,
  TagIcon,
  CakeIcon,
  PhotoIcon,
  ScissorsIcon,
  SwatchIcon,
  DocumentDuplicateIcon,
  ChartBarIcon,
  FolderIcon,
  PrinterIcon,
  ArrowDownTrayIcon,
  AtSymbolIcon,
  ShareIcon,
  TruckIcon
} from "@heroicons/vue/24/outline";
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
import {active} from '@/Composables/Extensions';
import {Extension} from '@/Enums/Extension';
import axios from 'axios';

const props = defineProps({
  patientId: {
    type: String,
    required: true
  }
})

const showSaveRecheck = ref(false);
const showSavePrescription = ref(false);
const showSaveNutrition = ref(false);
const showSaveHousing = ref(false);
const showSaveVaccine = ref(false);
const showPrintModal = ref(false);
const showExportModal = ref(false);
const showEmailModal = ref(false);
const currentRoute = ref(route().current());

const tabs = ref([
  can(Abilities.COMPUTED_VIEW_RESCUER) ? { name: __('Rescuer'), route: 'patients.rescuer.edit' } : false,
  { name: __('Initial Care'), route: 'patients.initial.edit' },
  { name: __('Continued Care'), route: 'patients.continued.edit' },
].filter(Boolean));

const dailyTasksOptionGroups = ref([
  [
    { name: __('Daily Tasks Due Now'), icon: CalendarIcon, action: 'patients.daily-tasks.edit' },
    { name: __('Daily Tasks Past Due'), icon: BellAlertIcon, action: 'patients.past-due-tasks.edit' },
    { name: __('All Scheduled Tasks'), icon: CalendarDaysIcon, action: 'patients.scheduled-tasks.edit' }
  ],
  [
    { name: __('Add New Recheck'), icon: ClockIcon, action: 'recheck' },
    { name: __('Add New Prescription'), icon: TagIcon, action: 'prescription' },
    { name: __('New Nutrition Plan'), icon: CakeIcon, action: 'nutrition' },
    // { name: __('New Housing Plan'), icon: HomeIcon, action: 'housing' },
    // { name: __('New Vaccine'), icon: EyeDropperIcon, action: 'vaccine' },
  ]
]);

const moreOptionGroups = ref([
    [
      { name: __('Attachments'), icon: PhotoIcon, route: 'patients.attachments.edit', can: usePage().props.subscription.isProPlan && active(Extension.ATTACHMENTS) },
      { name: __('Necropsy'), icon: ScissorsIcon, route: 'patients.necropsy.edit', can: active(Extension.NECROPSY) },
      { name: __('Banding and Morphometrics'), icon: SwatchIcon, route: 'patients.banding_morphometrics.edit', can: active(Extension.BANDING_MORPHOMETRICS) },
    ].filter(o => o.can),
    [
      { name: __('Duplicate Patient'), icon: DocumentDuplicateIcon, route: 'patients.duplicate.create', can: can(Abilities.CREATE_PATIENTS) },
      { name: __('Patient Analytics'), icon: ChartBarIcon, route: 'patients.analytics', can: true }
    ].filter(o => o.can),
    [
      { name: __('Revisions'), icon: FolderIcon, route: 'patients.revisions.index', can: can(Abilities.COMPUTED_DISPLAY_REVISIONS) }
    ].filter(o => o.can),
  ]
  .filter(array => array.length));

const share = ref([
  can(Abilities.SHARE_PATIENTS) ? [
    { name: __('Print Patient Medical Record'), icon: PrinterIcon, action: 'print' },
    { name: __('Export Patient to Spreadsheet'), icon: ArrowDownTrayIcon, action: 'export' },
    { name: __('Email Patient Medical Record'), icon: AtSymbolIcon, action: 'email' },
  ] : [],
  [
    { name: __('Print Necropsy Report'), icon: ScissorsIcon, action: route('reports.generate', {
      report: 'necropsy-report',
      format: 'pdf',
      patientId: props.patientId
    }), can: active(Extension.NECROPSY) },
  ].filter(o => o.can),
  //.concat(extension.navigation('patient.tabs.share')) : [],
  //[extension.navigation('patient.tabs.share.group').flat(1)].flat(),
  [
    { name: __('Transfer Patient'), icon: ShareIcon, action: 'share.transfer.create', can: can(Abilities.COMPUTED_VIEW_TRANSFER_PATIENT) }
  ].filter(o => o.can),
  [
    { name: __('Directions to Address Found'), icon: TruckIcon, action: 'directions' },
  ]
].filter(array => array.length));

const caseQueryString = ref({
  y: usePage().props.admission.case_year,
  c: usePage().props.admission.case_id,
});

const admission = computed(() => usePage().props.admission);

const handleSelectedItemChange = () => handleMore(currentRoute.value);

const handleDailyTasksAction = (action) => {
  const dictionary = {
    recheck: () => showSaveRecheck.value = true,
    prescription: () => showSavePrescription.value = true,
    nutrition: () => showSaveNutrition.value = true,
    housing: () => showSaveHousing.value = true,
    vaccine: () => showSaveVaccine.value = true,
  };
  if (Object.hasOwn(dictionary, action)) {
    dictionary[action]();
  } else if (route().has(action)) {
    router.get(route(action, caseQueryString.value));
  }
};

const handleMore = (action) => {
  const dictionary = {
    print: () => showPrintModal.value = true,
    export: () => showExportModal.value = true,
    email: () => showEmailModal.value = true,
    directions: () => {
      let accountAddress = usePage().props.auth.team.formatted_inline_address;
      let foundAddress = `${admission.value.patient.address_found} ${admission.value.patient.city_found} ${admission.value.patient.subdivision_found}`;

      window.open(`http://maps.google.com/maps?saddr=${ accountAddress }&daddr=${ foundAddress }`, '_blank').focus();
    }
  };

  if (Object.hasOwn(dictionary, action)) {
    dictionary[action]();
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
            v-for="item in dailyTasksOptionGroups.flat()"
            :key="item.name"
            :value="item.action"
          >
            {{ item.name }}
          </option>
        </optgroup>
        <optgroup :label="__('More')">
          <option
            v-for="item in moreOptionGroups.flat()"
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
        <!-- DailyTasks Menu -->
        <Menu
          as="div"
          class="relative"
        >
          <MenuButton class="relative inline-flex items-center px-3 py-2 border-b-2 border-transparent text-gray-600 hover:border-blue-400 hover:text-gray-900 font-medium text-sm whitespace-nowrap">
            {{ __('Daily Tasks') }}
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
                v-for="(dailyTasksOptions, i) in dailyTasksOptionGroups"
                :key="i"
              >
                <MenuItem
                  v-for="item in dailyTasksOptions"
                  :key="item.name"
                  v-slot="{ active }"
                >
                  <button
                    :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex items-center w-full px-4 py-2 text-sm']"
                    @click="handleDailyTasksAction(item.action)"
                  >
                    <Component
                      :is="item.icon"
                      class="h-5 w-5 mr-2"
                    />
                    {{ item.name }}
                  </button>
                </MenuItem>
              </div>
            </MenuItems>
          </transition>
        </Menu>
        <!-- More Menu -->
        <Menu
          as="div"
          class="relative"
        >
          <MenuButton class="relative inline-flex items-center px-3 py-2 border-b-2 border-transparent text-gray-600 hover:border-blue-400 hover:text-gray-900 font-medium text-sm whitespace-nowrap">
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
            <MenuItems class="origin-top-right absolute right-0 z-10 mt-2 -mr-1 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none divide-y-4 divide-gray-100">
              <div
                v-for="(moreOptions, i) in moreOptionGroups"
                :key="i"
              >
                <MenuItem
                  v-for="tab in moreOptions"
                  :key="tab.name"
                  v-slot="{ active }"
                >
                  <Link
                    :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex items-center w-full px-4 py-2 text-sm']"
                    :href="route(tab.route, caseQueryString)"
                  >
                    <Component
                      :is="tab.icon"
                      class="h-5 w-5 mr-2"
                    />
                    {{ tab.name }}
                  </Link>
                </MenuItem>
              </div>
            </MenuItems>
          </transition>
        </Menu>
        <!-- Sharing Menu -->
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
                      :is="item.icon"
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
  <SaveRecheckModal
    v-if="showSaveRecheck"
    :patientId="admission.patient_id"
    :show="true"
    :title="__('New Recheck')"
    @close="showSaveRecheck = false"
  />
  <SavePrescriptionModal
    v-if="showSavePrescription"
    :patientId="admission.patient_id"
    :show="true"
    :title="__('New Prescription')"
    @close="showSavePrescription = false"
  />
  <SaveNutritionModal
    v-if="showSaveNutrition"
    :patientId="admission.patient_id"
    :show="true"
    :title="__('New Nutrition')"
    @close="showSaveNutrition = false"
  />
  <PrintPatientModal
    :patientId="admission.patient_id"
    :show="showPrintModal"
    @close="showPrintModal = false"
  />
  <ExportPatientModal
    :patientId="admission.patient_id"
    :show="showExportModal"
    @close="showExportModal = false"
  />
  <EmailPatientModal
    :patientId="admission.patient_id"
    :show="showEmailModal"
    @close="showEmailModal = false"
  />
</template>
