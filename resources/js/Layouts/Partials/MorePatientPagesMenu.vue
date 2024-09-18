<script setup>
import { inject, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { ChevronDownIcon } from '@heroicons/vue/24/outline';
import * as heroIcons from "@heroicons/vue/24/outline";
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const route = inject('route');

const emits = defineEmits(['ready']);

const tabGroups = [
    [
      usePage().props.extensions['patient.tabs.more'] || []
    ].flat(),
    [(usePage().props.extensions['patient.tabs.more.group'] || []).flat(1)].flat(),
    [
      can(Abilities.CREATE_PATIENTS) ? { name: __('Duplicate Patient'), icon: 'DocumentDuplicateIcon', route: 'patients.duplicate.create' } : false,
      { name: __('Patient Analytics'), route: 'patients.analytics', icon: 'ChartBarIcon' }
    ].filter(Boolean),
    [
      can(Abilities.COMPUTED_DISPLAY_REVISIONS) ? { name: __('Revisions'), route: 'patients.revisions.index', icon: 'FolderIcon' } : false
    ].filter(Boolean),
  ]
  .filter(array => array.length);

const admission = computed(() => usePage().props.admission);

const isCurrent = computed(() => {
  return tabGroups.flat().map(task => task.route).includes(route().current())
});

const caseQueryString = computed(() => {
  return {
    y: admission.value.case_year,
    c: admission.value.case_id,
  }
});

onMounted(() => emits('ready', {tabGroups: tabGroups}))
</script>

<template>
  <Menu
    as="div"
    class="relative"
  >
    <MenuButton
      :class="[isCurrent ? 'bg-blue-400 text-gray-800 rounded-md' : '']"
      class="relative inline-flex items-center px-3 py-2 border-b-2 border-transparent text-gray-600 hover:border-blue-400 hover:text-gray-900 font-medium text-sm whitespace-nowrap"
    >
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
          v-for="(tabs, i) in tabGroups"
          :key="i"
        >
          <MenuItem
            v-for="tab in tabs"
            :key="tab.name"
            v-slot="{ active }"
          >
            <Link
              :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex items-center w-full px-4 py-2 text-sm']"
              :href="route(tab.route, caseQueryString)"
            >
              <Component
                :is="heroIcons[tab.icon]"
                class="h-5 w-5 mr-2"
              />
              {{ tab.name }}
            </Link>
          </MenuItem>
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>
