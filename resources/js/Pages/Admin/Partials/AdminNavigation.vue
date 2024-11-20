<script setup>
import {ref} from 'vue';
import {router} from '@inertiajs/vue3';
import {
  Cog6ToothIcon,
  GlobeAmericasIcon,
  AdjustmentsVerticalIcon,
  UserGroupIcon,
  CheckBadgeIcon,
  ChatBubbleBottomCenterTextIcon,
  CloudIcon,
  LanguageIcon
} from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';

const navigation = [
  { name: 'Admin Dashboard', route: 'admin.dashboard', icon: Cog6ToothIcon },
  { name: 'Accounts', route: 'teams.index', icon: UserGroupIcon },
  { name: 'Authorizations', route: 'admin.authorization', icon: CheckBadgeIcon },
  { name: 'Testimonials', route: 'admin.testimonials.index', icon: ChatBubbleBottomCenterTextIcon },
  { name: 'Maintenance', route: 'admin.maintenance', icon: AdjustmentsVerticalIcon },
  { name: 'Localization', route: 'admin.localization', icon: GlobeAmericasIcon },
];

const currentRoute = ref(route().current())

const redirectToTab = () => router.get(route(currentRoute.value));
</script>

<template>
  <aside>
    <h1 class="text-2xl font-semibold text-gray-900 mb-8">
      {{ __('WRMD Admin') }}
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
        <option
          v-for="item in navigation"
          :key="item.name"
          :value="item.route"
        >
          {{ item.name }}
        </option>
      </select>
    </div>
    <div class="hidden md:block">
      <nav class="space-y-1">
        <Link
          v-for="item in navigation"
          :key="item.name"
          :href="route(item.route)"
          :class="[route().current(item.route) ? 'bg-blue-300 text-gray-900 hover:bg-blue-300' : 'text-gray-700 hover:text-gray-900 hover:bg-blue-300', 'group rounded-md px-3 py-2 flex items-center text-sm font-medium']"
          :aria-current="item.current ? 'page' : undefined"
        >
          <component
            :is="item.icon"
            :class="[item.current ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500', 'flex-shrink-0 -ml-1 mr-3 h-6 w-6']"
            aria-hidden="true"
          />
          <span class="truncate">
            {{ item.name }}
          </span>
        </Link>
      </nav>
    </div>
  </aside>
</template>
