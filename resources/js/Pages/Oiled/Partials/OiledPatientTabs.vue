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
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
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

<script setup>
  import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
  import { ChevronDownIcon } from '@heroicons/vue/24/outline';
  import DailyTasksMenu from '@/Layouts/Partials//DailyTasksMenu.vue';
  import PrintPatientModal from '@/Layouts/Partials/PrintPatientModal.vue';
  import ExportPatientModal from '@/Layouts/Partials/ExportPatientModal.vue';
  import EmailPatientModal from '@/Layouts/Partials/EmailPatientModal.vue';
 import {
    PrinterIcon, ArrowDownTrayIcon, AtSymbolIcon, IdentificationIcon,
    ClipboardDocumentListIcon, BeakerIcon, PhotoIcon, SwatchIcon, DevicePhoneMobileIcon, ScissorsIcon
  } from '@heroicons/vue/24/outline';
</script>

<script>

export default {
  data() {
    const isCollectedAlive = this.$page.props.admission.patient?.event_processing.collection_condition === 'Alive';
    return {
      showPrintModal: false,
      showExportModal: false,
      showEmailModal: false,
      currentRoute: this.route().current(),
      tabs: [
        isCollectedAlive ? { name: this.__('Field Stabilization'), route: 'oiled.field.edit' } : false,
        { name: this.__('Processing'), route: 'oiled.processing.edit' },
        isCollectedAlive ? { name: this.__('Intake'), route: 'oiled.intake.edit' } : false,
        isCollectedAlive ? { name: this.__('Wash'), route: 'oiled.wash.index' } : false,
        isCollectedAlive ? { name: this.__('Conditioning'), route: 'oiled.conditioning.index' } : false,
      ].filter(Boolean),
      dailyTasks: [],
      more: [
        [
          { name: this.__('Case Summary'), icon: IdentificationIcon, action: 'oiled.summary.edit' },
          { name: this.__('Daily Exams'), icon: ClipboardDocumentListIcon, action: 'patients.exam.index' },
          { name: this.__('Labs'), icon: BeakerIcon, action: 'patients.lab.index' },
          { name: this.__('Attachments'), icon: PhotoIcon, action: 'patients.attachments.edit' },
          { name: this.__('Banding & Morphometrics'), icon: SwatchIcon, action: 'patients.research.edit' },
          { name: this.__('Necropsy'), icon: ScissorsIcon, action: 'patients.necropsy.edit' },
          { name: this.__('Wildlife Recovery Data'), icon: DevicePhoneMobileIcon, action: 'owcn.wrapp.index' },
        ],
        [
          { name: this.__('Print Cage Card Label'), icon: PrinterIcon, action: 'print' },
          { name: this.__('Print Evidence Label'), icon: PrinterIcon, action: 'print' },
        ],
        [
          { name: this.__('Print Patient Medical Record'), icon: PrinterIcon, action: 'print' },
          { name: this.__('Export Patient to Spreadsheet'), icon: ArrowDownTrayIcon, action: 'export' },
          { name: this.__('Email Patient Medical Record'), icon: AtSymbolIcon, action: 'email' },
        ]
      ].filter(array => array.length),
      dailyTasks: [],
      caseQueryString: {
        y: this.$page.props.admission.case_year,
        c: this.$page.props.admission.case_id,
      },
    }
  },
  computed: {
    admission() {
      return this.$page.props.admission;
    },
  },
  methods: {
    handleSelectedItemChange() {
      this.handleMore(this.currentRoute)
    },
    handleMore(action) {
      const handleMore = {
        print: () => this.showPrintModal = true,
        export: () => this.showExportModal = true,
        email: () => this.showEmailModal = true,
        directions: () => {
          let accountAddress = this.$page.props.auth.account.full_address;
          let foundAddress = this.admission.patient.location_found;
          window.open(`http://maps.google.com/maps?saddr=${ accountAddress }&daddr=${ foundAddress }`, '_blank').focus();
        }
      };

      if (Object.hasOwn(handleMore, action)) {
        handleMore[action]();
      } else if (this.route().has(action)) {
        this.$inertia.get(this.route(action, this.caseQueryString));
      } else {
        // This is very specific to generating a report
        window.axios.post(action)
      }
    }
  }
}
</script>
