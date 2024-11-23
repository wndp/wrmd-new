<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import { MagnifyingGlassIcon, PlusIcon } from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';

defineProps({
  subAccounts: {
    type: Array,
    required: true
  }
})
</script>

<template>
  <AppLayout title="Sub-Accounts">
    <div class="sm:flex sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">
          {{ __('Sub-Accounts') }}
        </h1>
        <h4 class="mt-1 text-sm text-gray-500">
          {{ __('Total') }}: {{ subAccounts.length }}
        </h4>
      </div>
      <div class="mt-3 sm:mt-0 sm:ml-4">
        <label
          for="mobile-search-candidate"
          class="sr-only"
        >{{ __('Search') }}</label>
        <label
          for="desktop-search-candidate"
          class="sr-only"
        >{{ __('Search') }}</label>
        <div class="flex rounded-md shadow-sm">
          <div class="relative flex-grow focus-within:z-10">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <MagnifyingGlassIcon
                class="h-5 w-5 text-gray-400"
                aria-hidden="true"
              />
            </div>
            <input
              id="mobile-search-candidate"
              type="text"
              name="mobile-search-candidate"
              class="focus:ring-blue-500 focus:border-blue-500 block w-full rounded-none rounded-l-md pl-10 sm:hidden border-gray-300"
              :placeholder="__('Search')"
            >
            <input
              id="desktop-search-candidate"
              type="text"
              name="desktop-search-candidate"
              class="hidden focus:ring-blue-500 focus:border-blue-500 w-full rounded-none rounded-l-md pl-10 sm:block sm:text-sm border-gray-300"
              :placeholder="__('Search')"
            >
          </div>
          <PrimaryButton
            class="rounded-l-none"
            @click="$inertia.get(route('sub_accounts.create'))"
          >
            <PlusIcon
              class="h-5 w-5"
              aria-hidden="true"
            />
            <span class="ml-2 whitespace-nowrap">New Sub-Account</span>
          </PrimaryButton>
        </div>
      </div>
    </div>
    <div class="flex flex-col mt-8">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200">
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
                    {{ __('Contact Name') }}
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Address') }}
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ __('Date Created') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="subAccount in subAccounts"
                  :key="subAccount.id"
                >
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-800">
                    <Link
                      :href="route('sub_accounts.show', { subAccount })"
                      class="hover:text-blue-900"
                    >
                      {{ subAccount.name }}
                    </Link>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ subAccount.contact_name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ subAccount.locale }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ subAccount.created_at_for_humans }}
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
