<script setup>
import {ref} from 'vue';
import {usePage, router} from '@inertiajs/vue3';
import {__} from '@/Composables/Translate';

const tabs = [
    { name: __('Tasks Due'), route: 'patients.daily-tasks.edit' },
    { name: __('Past Due'), route: 'patients.past-due-tasks.edit' },
    { name: __('All Scheduled Tasks'), route: 'patients.scheduled-tasks.edit' }
];

const currentRoute = ref(route().current());

const caseQueryString = ref({
  y: usePage().props.admission.case_year,
  c: usePage().props.admission.case_id
});

const redirectToTab = () => router.visit(route(currentRoute.value, caseQueryString));
</script>

<template>
  <div>
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
        @change="redirectToTab"
      >
        <option
          v-for="tab in tabs"
          :key="tab.name"
          :value="tab.route"
        >
          {{ tab.name }}
        </option>
      </select>
    </div>
    <div class="hidden sm:block">
      <nav
        class="relative z-0 shadow flex divide-x divide-gray-200"
        aria-label="Tabs"
      >
        <Link
          v-for="(tab, tabIdx) in tabs"
          :key="tab.name"
          :href="route(tab.route, caseQueryString)"
          :class="[tab.route === currentRoute ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700', tabIdx === 0 ? 'rounded-tl-lg' : '', tabIdx === tabs.length - 1 ? 'rounded-tr-lg' : '', 'group relative min-w-0 flex-1 overflow-hidden bg-white py-4 px-4 text-sm font-medium text-center hover:bg-gray-50 focus:z-10']"
          :aria-current="tab.route === currentRoute ? 'page' : undefined"
          preserveScroll
        >
          <span>{{ tab.name }}</span>
          <span
            aria-hidden="true"
            :class="[tab.route === currentRoute ? 'bg-blue-500' : 'bg-transparent', 'absolute inset-x-0 bottom-0 h-0.5']"
          />
        </Link>
      </nav>
    </div>
  </div>
</template>
