<script setup>
import {ref} from 'vue';
import {router} from '@inertiajs/vue3';
import {__} from '@/Composables/Translate';

const tabs = [
    { name: __('Open'), route: 'hotline.open.index' },
    { name: __('Resolved'), route: 'hotline.resolved.index' },
    { name: __('Unresolved'), route: 'hotline.unresolved.index' },
    { name: __('Deleted'), route: 'hotline.deleted.index' }
];

const currentRoute = ref(route().current());

const redirectToTab = () => router.get(route(currentRoute.value));
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
    <div class="hidden sm:block border-b border-gray-200 pb-1">
      <nav
        class="flex space-x-1 md:space-x-2 lg:space-x-4"
        aria-label="Tabs"
      >
        <Link
          v-for="tab in tabs"
          :key="tab.name"
          :href="route(tab.route)"
          :class="[tab.route === currentRoute ? 'bg-blue-300 text-gray-800' : 'text-gray-600 hover:text-gray-900', 'px-3 py-2 font-medium text-sm rounded-md whitespace-nowrap']"
          :aria-current="tab.route === currentRoute ? 'page' : undefined"
        >
          {{ tab.name }}
        </Link>
      </nav>
    </div>
  </div>
</template>
