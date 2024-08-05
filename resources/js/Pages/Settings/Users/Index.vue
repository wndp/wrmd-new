<template>
  <AppLayout title="Users">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <div class="flex flex-col">
          <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6 sm:rounded-t-lg">
            <div class="-ml-4 -mt-2 flex items-center justify-between flex-wrap sm:flex-nowrap">
              <div class="ml-4 mt-2">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  {{ __('Users') }}
                </h3>
              </div>
              <div class="ml-4 mt-2 flex-shrink-0">
                <Link
                  :href="route('users.create')"
                  class="relative inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  {{ __('Add New User') }}
                </Link>
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
                        <Link
                          :href="route('users.edit', user.id)"
                          class="hover:text-gray-900"
                        >
                          {{ user.name }}
                        </Link>
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
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsAside from '../Partials/SettingsAside.vue';

defineProps({
  users: {
    type: Array,
    required: true
  }
});
</script>
