<script setup>
import { inject, ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import SaveRecheckModal from '@/Pages/DailyTasks/Partials/SaveRecheckModal.vue';
import SavePrescriptionModal from '@/Pages/DailyTasks/Partials/SavePrescriptionModal.vue';
import SaveNutritionModal from '@/Pages/DailyTasks/Partials/SaveNutritionModal.vue';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { ChevronDownIcon } from '@heroicons/vue/24/outline';
import * as heroIcons from "@heroicons/vue/24/outline";
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const route = inject('route');

const emits = defineEmits(['ready']);

const dailyTasks = [
  [
    { name: __('Daily Tasks Due Now'), icon: 'CalendarIcon', action: 'patients.daily-tasks.edit' },
    { name: __('Daily Tasks Past Due'), icon: 'BellAlertIcon', action: 'patients.past-due-tasks.edit' },
    { name: __('All Scheduled Tasks'), icon: 'CalendarDaysIcon', action: 'patients.scheduled-tasks.edit' }
  ],
  [
    { name: __('Add New Recheck'), icon: 'ClockIcon', action: 'recheck' },
    { name: __('Add New Prescription'), icon: 'TagIcon', action: 'prescription' },
    { name: __('New Nutrition Plan'), icon: 'CakeIcon', action: 'nutrition' },
    // { name: __('New Housing Plan'), icon: 'HomeIcon', action: 'housing' },
    // { name: __('New Vaccine'), icon: 'EyeDropperIcon', action: 'vaccine' },
  ]
];

const admission = computed(() => usePage().props.admission);

const isCurrent = computed(() => {
  return dailyTasks[0].map(task => task.action).includes(route().current())
});

const caseQueryString = computed(() => {
  return {
    y: admission.value.case_year,
    c: admission.value.case_id,
  }
});

const handleAction = (action) => {
  const handleAction = {
    recheck: () => showSaveRecheck.value = true,
    prescription: () => showSavePrescription.value = true,
    nutrition: () => showSaveNutrition.value = true,
    housing: () => showSaveHousing.value = true,
    vaccine: () => showSaveVaccine.value = true,
  };
  if (Object.hasOwn(handleAction, action)) {
    handleAction[action]();
  } else if (route().has(action)) {
    router.get(route(action, caseQueryString.value));
  }
};

const showSaveRecheck = ref(false);
const showSavePrescription = ref(false);
const showSaveNutrition = ref(false);
const showSaveHousing = ref(false);
const showSaveVaccine = ref(false);

onMounted(() => emits('ready', {tabGroups: dailyTasks}))
</script>

<template>
  <Menu
    v-if="can(Abilities.VIEW_DAILY_TASKS)"
    as="div"
    class="relative"
  >
    <MenuButton
      :class="[isCurrent ? 'bg-blue-400 text-gray-800 rounded-md' : '']"
      class="relative inline-flex items-center px-3 py-2 border-b-2 border-transparent text-gray-600 hover:border-blue-400 hover:text-gray-900 font-medium text-sm whitespace-nowrap"
    >
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
          v-for="(items, i) in dailyTasks"
          :key="i"
        >
          <MenuItem
            v-for="item in items"
            :key="item.name"
            v-slot="{ active }"
          >
            <button
              :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex items-center w-full px-4 py-2 text-sm']"
              @click="handleAction(item.action)"
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
</template>
