<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import SubAccountTabs from './Partials/SubAccountTabs.vue';
import ExtensionToggler from '@/Components/ExtensionToggler.vue';
import {__} from '@/Composables/Translate';

defineProps({
  subAccount: {
    type: Object,
    required: true
  },
  extensions: {
    type: Array,
    required: true
  }
});
</script>

<template>
  <AppLayout title="Sub-Accounts">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ subAccount.name }}
      </h1>
    </template>
    <Link
      :href="route('sub_accounts.index')"
      class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
    >
      <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
      {{ __('Return to Sub-Accounts List') }}
    </Link>
    <SubAccountTabs
      :subAccount="subAccount"
      class="mt-4"
    />
    <ul
      role="list"
      class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 mt-8"
    >
      <ExtensionToggler
        v-for="extension in extensions"
        :key="extension.namespace"
        :extension="extension"
        :team="subAccount"
      />
    </ul>
  </AppLayout>
</template>
