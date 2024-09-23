<script setup>
import { ref } from 'vue';
import SaveRecheckModal from './SaveRecheckModal.vue';
import SavePrescriptionModal from './SavePrescriptionModal.vue';
import SaveNutritionModal from './SaveNutritionModal.vue';
import SaveCommentModal from './SaveCommentModal.vue';
import { TransitionRoot, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import * as heroIcons from "@heroicons/vue/24/outline";
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

defineProps({
    patientId: {
        type: Number,
        required: true
    }
});

const dailyTasks = [
  { name: __('New Recheck'), icon: 'ClockIcon', action: 'recheck' },
  { name: __('New Prescription'), icon: 'TagIcon', action: 'prescription' },
  { name: __('New Nutrition Plan'), icon: 'CakeIcon', action: 'nutrition' },
  // { name: __('New Housing Plan'), icon: 'HomeIcon', action: 'housing' },
  // { name: __('New Vaccine'), icon: 'ShieldCheckIcon', action: 'vaccine' },
  { name: __('New Comment'), icon: 'ChatBubbleBottomCenterIcon', action: 'comment' },
];

const handleAction = (action) => {
  const handleAction = {
    recheck: () => showSaveRecheck.value = true,
    prescription: () => showSavePrescription.value = true,
    nutrition: () => showSaveNutrition.value = true,
    housing: () => showSaveHousing.value = true,
    vaccine: () => showSaveVaccine.value = true,
    comment: () => showSaveComment.value = true,
  };
  if (Object.hasOwn(handleAction, action)) {
    handleAction[action]();
  }
};

const showSaveRecheck = ref(false);
const showSavePrescription = ref(false);
const showSaveNutrition = ref(false);
const showSaveHousing = ref(false);
const showSaveVaccine = ref(false);
const showSaveComment = ref(false);
</script>

<template>
  <Menu
    v-if="can(Abilities.MANAGE_DAILY_TASKS)"
    as="div"
    class="relative inline-block text-left"
  >
    <MenuButton>
      <slot>
        <div class="inline-flex justify-center items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-1 bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300">
          <Component
            :is="heroIcons['PlusIcon']"
            class="h-5 w-5 flex-shrink-0 mr-1"
            aria-hidden="true"
          />
          <span class="sr-only">{{ __('Add Task') }}</span>
          <span>{{ __('Add Task') }}</span>
        </div>
      </slot>
    </MenuButton>
    <TransitionRoot
      enterActive="transition ease-out duration-100"
      enterFrom="transform opacity-0 scale-95"
      enterTo="transform opacity-100 scale-100"
      leaveActive="transition ease-in duration-75"
      leaveFrom="transform opacity-100 scale-100"
      leaveTo="transform opacity-0 scale-95"
    >
      <MenuItems class="origin-top-left absolute z-30 left-0 mt-2 -mr-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none divide-y-4 divide-gray-100">
        <div>
          <MenuItem
            v-for="task in dailyTasks"
            :key="task.name"
            v-slot="{ active }"
          >
            <button
              type="button"
              :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex items-center w-full text-left px-4 py-2 text-sm']"
              @click="handleAction(task.action)"
            >
              <Component
                :is="heroIcons[task.icon]"
                class="h-5 w-5 mr-2"
              />
              {{ task.name }}
            </button>
          </MenuItem>
        </div>
      </MenuItems>
    </TransitionRoot>
  </Menu>
  <SaveRecheckModal
    v-if="showSaveRecheck"
    :patientId="patientId"
    :show="true"
    :title="__('New Recheck')"
    @close="showSaveRecheck = false"
  />
  <SavePrescriptionModal
    v-if="showSavePrescription"
    :patientId="patientId"
    :show="true"
    :title="__('New Prescription')"
    @close="showSavePrescription = false"
  />
  <SaveNutritionModal
    v-if="showSaveNutrition"
    :patientId="patientId"
    :show="true"
    :title="__('New Nutrition Plan')"
    @close="showSaveNutrition = false"
  />
  <SaveCommentModal
    v-if="showSaveComment"
    :patientId="patientId"
    :show="true"
    :title="__('New Comment')"
    @close="showSaveComment = false"
  />
</template>
