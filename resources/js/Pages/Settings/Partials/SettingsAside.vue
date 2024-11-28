<script setup>
import {ref} from 'vue';
import {router} from '@inertiajs/vue3';
import {
  UserCircleIcon,
  UsersIcon,
  BriefcaseIcon,
  EyeSlashIcon,
  ShieldCheckIcon,
  TagIcon,
  CogIcon,
  CloudIcon,
  AcademicCapIcon,
  PuzzlePieceIcon,
} from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';

const navigation = [
  { name: __('Account Profile'), route: 'account.profile.edit', icon: UserCircleIcon },
  { name: __('Users & Staff'), route: 'users.index', icon: UsersIcon },
  { name: __('Veterinarians'), route: 'veterinarians.index', icon: BriefcaseIcon },
  { name: __('People Privacy'), route: 'privacy.edit', icon: EyeSlashIcon },
  { name: __('Security'), route: 'security.edit', icon: ShieldCheckIcon },
  { name: __('Classification Tagging'), route: 'classification-tagging.edit', icon: TagIcon },
  { name: __('General WRMD Settings'), route: 'general-settings.edit', icon: CogIcon },
  { name: __('Data Sharing'), route: 'data-sharing.edit', icon: AcademicCapIcon },
  { name: __('API'), route: 'api.index', icon: CloudIcon },
  { name: __('Extensions'), route: 'extensions.index', icon: PuzzlePieceIcon },
];

const currentRoute = ref(route().current())

const redirectToTab = () => router.get(route(currentRoute.value));
</script>

<template>
  <aside>
    <h1 class="text-2xl font-semibold text-gray-900 mb-8">
      {{ __('Settings') }}
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
