<script setup>
import {ref, onMounted, computed} from 'vue';
import {router} from '@inertiajs/vue3';

const currentRoute = ref(route().current())

const redirectToTab = () => router.get(route(currentRoute.value));

const tabs = ref([
  { name: 'Taxa Dashboard', route: 'taxa.index' },
  { name: 'Unrecognized Patients', route: 'taxa.unrecognized.index' },
  { name: 'Misidentified Patients', route: 'taxa.misidentified.index' },
]);

// export default {
//   data() {
//     return {
//         currentTab: this.route().current(),
//         tabs: [
//             { name: 'Taxa Dashboard', route: 'taxa.index' },
//             { name: 'Unrecognized Patients', route: 'taxa.unrecognized.index' },
//             { name: 'Misidentified Patients', route: 'taxa.misidentified.index' },
//         ]
//     };
//   },
//   watch: {
//     currentTab(value) {
//         this.$inertia.visit(this.route(value));
//     }
//   }
// };
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
        v-model="currentRoute"
        name="tabs"
        class="block w-full focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"
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
    <div class="hidden sm:block pb-1 border-b border-gray-300">
      <nav
        class="flex"
        aria-label="Tabs"
      >
        <Link
          v-for="tab in tabs"
          :key="tab.name"
          :href="route(tab.route, team)"
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
