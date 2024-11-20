<script setup>
import {ref, onMounted} from 'vue';
import {router} from '@inertiajs/vue3';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import { MapPinIcon } from '@heroicons/vue/24/solid';
import LocalStorage from '@/Composables/LocalStorage';
import URI from 'urijs';

const localStorage = LocalStorage();

defineProps({
  team: Object
});

const currentRoute = ref(route().current())

const redirectToTab = () => router.get(route(currentRoute.value));

const tabs = [
  { name: 'Details', route: 'teams.show' },
  { name: 'Profile', route: 'teams.edit' },
  { name: 'Users', route: 'teams.show.users' },
  { name: 'Unrecognized Patients ', route: 'teams.show.unrecognized' },
  { name: 'Misidentified Patients ', route: 'teams.show.misidentified' },
  { name: 'Extensions ', route: 'teams.extensions.edit' },
  { name: 'Meta ', route: 'teams.show.meta' },
  { name: 'Actions ', route: 'teams.show.actions' },
];

onMounted(() => localStorage.store('peopleFilters', new URI().query(true)));

// export default {

//   data() {
//     return {
//       //currentRoute: this.route().current(),
//       // tabs: [
//       //   { name: 'Details', route: 'accounts.show' },
//       //   { name: 'Profile', route: 'accounts.edit' },
//       //   { name: 'Users', route: 'accounts.show.users' },
//       //   { name: 'Unrecognized Patients ', route: 'accounts.show.unrecognized' },
//       //   { name: 'Misidentified Patients ', route: 'accounts.show.misidentified' },
//       //   { name: 'Extensions ', route: 'accounts.extensions.edit' },
//       //   { name: 'Meta ', route: 'accounts.show.meta' },
//       //   { name: 'Actions ', route: 'accounts.show.actions' },
//       // ]
//     };
//   },
//   computed: {
//     uri() {
//         return LocalStorage.get('accountFilters');
//     }
//   },
//   watch: {
//     currentRoute(value) {
//         this.$inertia.visit(this.route(value, {
//             account: this.account
//         }), {
//             preserveScroll: true
//         });
//     }
//   }
// };
</script>

<template>
  <div>
    <Link
      :href="route('teams.index', uri)"
      class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
    >
      <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
      Return to Accounts
    </Link>
    <div class="max-w-7xl mx-auto">
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ team.name }}
      </h1>
    </div>
    <div class="mt-6">
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
  </div>
</template>
