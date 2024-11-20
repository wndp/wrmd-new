<template>
  <AppLayout title="Accounts">
    <AccountHeader :account="account" />
    <div class="flex flex-col mt-8">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <caption class="px-2 py-3 text-left text-base font-bold text-gray-700 bg-white ">
                Settings
              </caption>
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Key
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Value
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Created
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="(setting, i) in account.settings"
                  :key="setting.id"
                  :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                >
                  <td class="px-2 py-4 whitespace-nowrap">
                    {{ setting.key }}
                  </td>
                  <td class="px-2 py-4 text-sm text-gray-900">
                    {{ setting.value }}
                  </td>
                  <td class="px-2 py-4 text-sm text-gray-900">
                    {{ setting.created_at }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="flex flex-col mt-8">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <caption class="px-2 py-3 text-left text-base font-bold text-gray-700 bg-white ">
                Custom Fields
              </caption>
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Name
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Type
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Options
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Panel
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Required?
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Created
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="(field, i) in account.custom_fields"
                  :key="field.id"
                  :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                >
                  <td class="px-2 py-4 whitespace-nowrap">
                    {{ field.label }}
                  </td>
                  <td class="px-2 py-4 text-sm text-gray-900">
                    {{ field.type }}
                  </td>
                  <td class="px-2 py-4 text-sm text-gray-900">
                    {{ field.options }}
                  </td>
                  <td class="px-2 py-4 text-sm text-gray-900">
                    {{ field.panel }}
                  </td>
                  <td class="px-2 py-4 text-sm text-gray-900">
                    {{ field.is_required ? 'Yes' : 'No' }}
                  </td>
                  <td class="px-2 py-4 text-sm text-gray-900">
                    {{ field.created_at }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <GeneratedReports :account-id="account.id" class="mt-8"/>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AccountHeader from './Partials/AccountHeader.vue';
import GeneratedReports from '@/Pages/Reports/Partials/GeneratedReports.vue';
</script>

<script>
export default {
    props: {
        account: Object
    },
};
</script>
