<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsAside from '../Partials/SettingsAside.vue';
import {__} from '@/Composables/Translate';

defineProps({
  veterinarians: Array
});
</script>

<template>
  <AppLayout title="Veterinarians">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <div class="flex flex-col">
          <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6 sm:rounded-t-lg">
            <div class="-ml-4 -mt-2 flex items-center justify-between flex-wrap sm:flex-nowrap">
              <div class="ml-4 mt-2">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  {{ __('Veterinarians') }}
                </h3>
              </div>
              <div class="ml-4 mt-2 flex-shrink-0">
                <Link
                  :href="route('veterinarians.create')"
                  class="relative inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  {{ __('Add New Veterinarian') }}
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
                        {{ __('License') }}
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        {{ __('Phone') }}
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        style="min-width: 200px;"
                      >
                        {{ __('Address') }}
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(veterinarian, i) in veterinarians"
                      :key="veterinarian.id"
                      :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                    >
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                        <Link
                          :href="route('veterinarians.edit', veterinarian.id)"
                          class="hover:text-gray-900"
                        >
                          {{ veterinarian.name }}
                        </Link>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ veterinarian.license }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ veterinarian.phone }}
                      </td>
                      <td class="px-6 py-4 text-sm text-gray-500" v-html="veterinarian.full_address">
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
