<template>
  <AppLayout title="Accounts">
    <AccountHeader :account="account" />
    <div class="flex flex-col mt-8">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    ID
                  </th>
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
                    Email
                  </th>
                  <th
                    scope="col"
                    class="px-2 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    Role
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
                  v-for="(user, i) in users"
                  :key="user.id"
                  :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                >
                  <td class="px-2 py-4 whitespace-nowrap">
                    {{ user.id }}
                  </td>
                  <td class="px-2 py-4 text-sm text-gray-900">
                    {{ user.name }}
                  </td>
                  <td class="px-2 py-4 text-sm text-gray-900">
                    {{ user.email }}
                  </td>
                  <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ user.role_name_for_humans }}
                  </td>
                  <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ user.created_at_diff_for_humans }}
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

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import AccountHeader from './Partials/AccountHeader.vue';

export default {
    components: {
        AppLayout,
        AccountHeader,
    },
    props: {
        account: Object,
        users: Array
    },
};
</script>
