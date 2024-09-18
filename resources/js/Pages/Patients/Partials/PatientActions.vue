<script setup>
import {ref} from 'vue';
import {useForm, router} from '@inertiajs/vue3';
import {Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue';
import {ChevronDownIcon} from '@heroicons/vue/24/solid';
import {
  XCircleIcon, PrinterIcon, RectangleStackIcon, AtSymbolIcon, ArrowDownTrayIcon, Bars3Icon
} from '@heroicons/vue/24/outline';
import PrintPatientModal from '@/Pages/Patients/Partials/PrintPatientModal.vue';
import ExportPatientModal from '@/Pages/Patients/Partials/ExportPatientModal.vue';
import EmailPatientModal from '@/Pages/Patients/Partials/EmailPatientModal.vue';
import {Abilities} from '@/Enums/Abilities';
import {can} from '@/Composables/Can';
import {__} from '@/Composables/Translate';
import axios from 'axios';

//const emit = defineEmits(['clear']);

const showPrintModal = ref(false);
const showExportModal = ref(false);
const showEmailModal = ref(false);
const actions = ref([
    can(Abilities.SHARE_PATIENTS) ? [
        { name: __('Print List of Selected Patients'), icon: PrinterIcon, action: 'printList' },
        { name: __('Export List of Selected Patients'), icon: ArrowDownTrayIcon, action: 'exportList' },
    ] : [],
    can(Abilities.SHARE_PATIENTS) ? [
        { name: __('Print Patients'), icon: PrinterIcon, action: 'print' },
        { name: __('Export Patients'), icon: ArrowDownTrayIcon, action: 'export' },
        { name: __('Email Patients'), icon: AtSymbolIcon, action: 'email' },
    ] : [],
    [
        { name: __('List Only Selected Patients'), icon: Bars3Icon, action: 'list' },
        can(Abilities.BATCH_UPDATE) ? { name: __('Batch Update Selected Patients'), icon: RectangleStackIcon, action: 'batch' } : false
    ].filter(Boolean)
].filter(array => array.length));

const act = (action) => {
  const actions = {
      clear: () => useForm({}).delete(route('select-patient.destroy'), {
          preserveState: false
      }),
      printList: () => axios.post(`/reports/generate/patients-list?format=pdf`),
      exportList: () => axios.post(`/reports/generate/patients-list?format=xlsx`),
      print: () => showPrintModal.value = true,
      export: () => showExportModal.value = true,
      email: () => showEmailModal.value = true,
      batch: () => router.visit(route('patients.batch.edit')),
      list: () => router.visit(route('patients.index', {
          list: 'selected-patients-list'
      }))
  };

  actions[action]();
}
</script>

<template>
  <div>
    <Menu
      as="span"
      class="-ml-px absolute block"
    >
      <MenuButton class="inline-flex text-sm font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75">
        {{ __('Actions') }}
        <ChevronDownIcon
          class="-mr-1 ml-2 h-5 w-5"
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
        <MenuItems class="origin-top-left absolute left-0 mt-2 -mr-1 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none divide-y-4 divide-gray-100">
          <div>
            <MenuItem v-slot="{ active }">
              <button
                :class="[active ? 'bg-gray-100 text-red-900' : 'text-red-700', 'flex items-center w-full px-4 py-2 text-sm']"
                @click="act('clear')"
              >
                <XCircleIcon class="h-4 w-4 mr-2" />
                {{ __('Un-select All Patients') }}
              </button>
            </MenuItem>
          </div>
          <div
            v-for="(items, i) in actions"
            :key="i"
          >
            <MenuItem
              v-for="item in items"
              :key="item.name"
              v-slot="{ active }"
            >
              <button
                :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex items-center w-full px-4 py-2 text-sm']"
                @click="act(item.action)"
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
    <PrintPatientModal
      :show="showPrintModal"
      @close="showPrintModal = false"
    />
    <ExportPatientModal
      :show="showExportModal"
      @close="showExportModal = false"
    />
    <EmailPatientModal
      :show="showEmailModal"
      @close="showEmailModal = false"
    />
  </div>
</template>
