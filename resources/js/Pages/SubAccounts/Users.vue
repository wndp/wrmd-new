<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import SubAccountTabs from './Partials/SubAccountTabs.vue';
import {__} from '@/Composables/Translate';

defineProps({
  subAccount: {
    type: Object,
    required: true
  },
  users: {
    type: Array,
    required: true
  }
})
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
    <div class="flex flex-col mt-8">
      <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6 sm:rounded-t-lg">
        <div class="-ml-4 -mt-2 flex items-center justify-between flex-wrap sm:flex-nowrap">
          <div class="ml-4 mt-2">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ __('Users') }}
            </h3>
            <p class="mt-2 text-sm text-gray-700">
              {{ __("To manage this sub-account's users, you must sign into it and navigate to the sub-account's settings section.") }}
            </p>
          </div>
        </div>
      </div>
      <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-b-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Name') }}
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Email') }}
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Role') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(user, i) in users"
                  :key="user.id"
                  :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                >
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                    {{ user.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ user.email }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ user.role_name_for_humans }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
