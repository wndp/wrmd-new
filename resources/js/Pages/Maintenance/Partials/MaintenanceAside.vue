<script setup>
import {ref} from 'vue';
import {router} from '@inertiajs/vue3';
import {
  QuestionMarkCircleIcon,
  ShareIcon,
  BookOpenIcon,
  CakeIcon,
  QueueListIcon,
  BanknotesIcon,
  InboxArrowDownIcon,
  XMarkIcon,
  TrashIcon
} from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
import {active} from '@/Composables/Extensions';
import {Extension} from '@/Enums/Extension';

const tabs = [
    { name: __('Unrecognized Patients'), route: 'maintenance.unrecognized-patients', icon: QuestionMarkCircleIcon, can: true },
    { name: __('Patient Transfers'), route: 'maintenance.transfers', icon: ShareIcon, can: true },
    { name: __('Prescription Formulary'), route: 'maintenance.formulas.index', icon: BookOpenIcon, can: true },
    { name: __('Nutrition Cookbook'), route: 'maintenance.cookbook.index', icon: CakeIcon, can: true },
    { name: __('Autocomplete'), route: 'maintenance.autocomplete.index', icon: QueueListIcon, can: true },
    { name: __('Expenses'), route: 'maintenance.expense_categories.index', icon: BanknotesIcon, can: active(Extension.EXPENSES) },
    { name: __('Import'), route: 'import.declaration.index', icon: InboxArrowDownIcon, can: can(Abilities.IMPORT) },
].filter(Boolean);

const dangerTabs = [
    { name: __('Delete a Patient'), route: 'patient.delete.index', icon: XMarkIcon, can: can(Abilities.DISPLAY_DANGER_ZONE) },
    { name: __('Delete all Patients'), route: 'year.delete.index', icon: TrashIcon, can: can(Abilities.DISPLAY_DANGER_ZONE) }
];

const currentRoute = ref(route().current())

const redirectToTab = () => router.get(route(currentRoute.value));
</script>

<template>
  <aside>
    <h1 class="text-2xl font-semibold text-gray-900 mb-8">
      {{ __('Maintenance') }}
    </h1>
    <div class="md:hidden my-4">
      <label
        for="tabs"
        class="sr-only"
      >{{ __('Select a tab') }}</label>
      <select
        id="tabs"
        v-model="currentRoute"
        name="tabs"
        class="block w-full focus:ring-blue-500 focus:border-blue-500 border-gray-300 rounded-md"
        @change="redirectToTab"
      >
        <template
          v-for="tab in tabs.concat(dangerTabs)"
          :key="tab.name"
        >
          <option
            v-if="tab.can || true"
            :value="tab.route"
          >
            {{ tab.name }}
          </option>
        </template>
      </select>
    </div>
    <div class="hidden md:block">
      <nav class="space-y-1">
        <template
          v-for="tab in tabs"
          :key="tab.name"
        >
          <Link
            v-if="tab.can || true"
            :href="route(tab.route)"
            :class="[tab.route === currentRoute ? 'bg-blue-300 text-gray-900 hover:bg-blue-300' : 'text-gray-700 hover:text-gray-900 hover:bg-blue-300', 'group rounded-md px-3 py-2 flex items-center text-sm font-medium']"
            :aria-current="tab.route === currentRoute ? 'page' : undefined"
          >
            <component
              :is="tab.icon"
              :class="[tab.route === currentRoute ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500', 'flex-shrink-0 -ml-1 mr-3 h-6 w-6']"
              aria-hidden="true"
            />
            <span class="truncate">
              {{ tab.name }}
            </span>
          </Link>
        </template>
        <template v-if="can(Abilities.VIEW_DANGER_ZONE)">
          <div>
            <div class="border-t border-gray-200 my-4" />
          </div>
          <Link
            v-for="tab in dangerTabs"
            :key="tab.name"
            :href="route(tab.route)"
            :class="[tab.route === currentRoute ? 'bg-red-300 text-red-900 hover:bg-red-300' : 'text-red-700 hover:text-red-900 hover:bg-red-300', 'group rounded-md px-3 py-2 flex items-center text-sm font-medium']"
            :aria-current="tab.route === currentRoute ? 'page' : undefined"
          >
            <component
              :is="tab.icon"
              :class="[tab.route === currentRoute ? 'text-red-500' : 'text-red-400 group-hover:text-red-500', 'flex-shrink-0 -ml-1 mr-3 h-6 w-6']"
              aria-hidden="true"
            />
            <span class="truncate">
              {{ tab.name }}
            </span>
          </Link>
        </template>
      </nav>
    </div>
  </aside>
</template>
