<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Panel.vue';
import { HomeIcon, PlusIcon, Bars3Icon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';

const navigation = [
    { name: __('Dashboard'), route: 'dashboard', icon: HomeIcon },
    { name: __('Add New Patient'), route: 'patients.create', icon: PlusIcon },
    { name: __('List Patients'), route: 'patients.index', icon: Bars3Icon },
    { name: __('Search Patients'), route: 'search.simple.create', icon: MagnifyingGlassIcon }
];
</script>

<template>
  <AppLayout title="Admissions">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 mb-8">
        {{ __('Patient Not Found') }}
      </h1>
    </template>
    <Panel>
      <template #heading>
        {{ __('What Would You Like To Do?') }}
      </template>
      <nav class="space-y-1">
        <Link
          v-for="item in navigation"
          :key="item.name"
          :href="route(item.route)"
          :class="[route().current(item.route) ? 'bg-blue-100 text-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center py-2 text-base font-medium rounded-md']"
        >
          <component
            :is="item.icon"
            :class="[route().current(item.route) ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500', 'mr-4 flex-shrink-0 h-6 w-6']"
            aria-hidden="true"
          />
          {{ item.name }}
        </Link>
      </nav>
    </Panel>
  </AppLayout>
</template>
