<script>
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import { MapPinIcon } from '@heroicons/vue/24/solid';

export default {
  data() {
    return {
      currentTab: this.route().current(),
      tabs: [
        { name: 'List Accounts', route: 'teams.index' },
        { name: 'Reports', route: 'teams.reports' },
      ]
    };
  },
  watch: {
    currentTab(value) {
        this.$inertia.visit(this.route(value));
    }
  }
};
</script>

<template>
  <div>
    <div class="sm:hidden">
      <label
        for="tabs"
        class="sr-only"
      >Select a tab</label>
      <select
        id="tabs"
        v-model="currentTab"
        name="tabs"
        class="block w-full focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"
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
    <div class="hidden sm:block pb-1 border-b border-gray-300">
      <nav
        class="flex"
        aria-label="Tabs"
      >
        <Link
          v-for="tab in tabs"
          :key="tab.name"
          :href="route(tab.route)"
          :class="[route().current(tab.route) ? 'bg-gray-200 text-gray-800' : 'text-gray-600 hover:text-gray-800', 'px-3 py-2 font-medium text-sm rounded-md']"
          :aria-current="route().current(tab.route) ? 'page' : undefined"
          preserveScroll
        >
          {{ tab.name }}
        </Link>
      </nav>
    </div>
  </div>
</template>
