<script setup>
import {ref} from 'vue';
import {router} from '@inertiajs/vue3';
import {
  InformationCircleIcon,
  IdentificationIcon,
  UsersIcon,
  PuzzlePieceIcon,
  CogIcon
} from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';

const props = defineProps({
  subAccount: {
    type: Object,
    required: true
  }
});

const tabs = [
    { name: __('Details'), icon: InformationCircleIcon, route: 'sub_accounts.show' },
    { name: __('Profile'), icon: IdentificationIcon, route: 'sub_accounts.edit' },
    { name: __('Users'), icon: UsersIcon, route: 'sub_accounts.users.index' },
    { name: __('Settings'), icon: CogIcon, route: 'sub_accounts.settings.edit' },
    { name: __('Extensions'), icon: PuzzlePieceIcon, route: 'sub_accounts.extensions.edit' },
];

const currentRoute = ref(route().current())

const redirectToTab = () => router.get(route(currentRoute.value, props.subAccount));
</script>

<script>
// export default {
//     data() {
//         return {

//             currentRoute: this.route().current(),
//         };
//     },
//     methods: {
//         redirectToTab() {
//             this.$inertia.get(this.route(this.currentRoute, this.subAccount));
//         },
//     }
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
          :href="route(tab.route, {subAccount})"
          :class="[tab.route === currentRoute ? 'bg-blue-300 text-gray-800' : 'text-gray-600 hover:text-gray-900', 'flex items-center px-3 py-2 font-medium text-sm rounded-md whitespace-nowrap']"
          :aria-current="tab.route === currentRoute ? 'page' : undefined"
        >
          <Component
            :is="tab.icon"
            class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6"
          />
          {{ tab.name }}
        </Link>
      </nav>
    </div>
  </div>
</template>
