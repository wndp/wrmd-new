<script setup>
import {usePage} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Paginator from '@/Components/Paginator.vue';
import Badge from '@/Components/Badge.vue';
import axios from 'axios';
import {__} from '@/Composables/Translate';

defineProps({
  notifications: {
    type: Object,
    required: true
  }
});

const markAsRead = (notificationId) => axios.delete('/notifications/' + notificationId);
</script>

<template>
  <AppLayout title="Notifications">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ __('Notifications') }}
      </h1>
    </template>
    <div class="flex flex-col mt-8">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="h-full overflow-y-auto relative">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-blue-100">
                <tr>
                  <th
                    scope="col"
                    class="px-3 py-3"
                  />
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    {{ __('Posted') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    {{ __('Status') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                  >
                    {{ __('Message') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="usePage().props.unreadNotifications.length">
                  <th
                    colspan="4"
                    class="px-3 py-3 sticky top-0 text-left font-bold text-base"
                  >
                    {{ __('Unread Notifications') }}
                  </th>
                </tr>
                <tr
                  v-for="notification in usePage().props.unreadNotifications.length"
                  :key="notification.id"
                >
                  <td class="px-3 py-3 whitespace-nowrap text-right text-sm">
                    <Link
                      v-if="notification.read_at"
                      :href="route('notifications.update', {notification})"
                      as="button"
                      method="put"
                      class="text-base leading-5 text-blue-600 hover:text-blue-700"
                    >
                      {{ __('Mark Unread') }}
                    </Link>
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-800">
                    {{ notification.created_at_for_humans }}
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                    <Badge :color="notification.read_at ? 'green' : 'yellow'">
                      {{ notification.read_at ? 'Read' : 'Unread' }}
                    </Badge>
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                    <Link
                      :href="notification.data.link"
                      @start="markAsRead(notification.id, $event)"
                    >
                      {{ notification.data.message }}
                    </Link>
                  </td>
                </tr>
                <tr v-if="usePage().props.unreadNotifications.length.length">
                  <th
                    colspan="4"
                    class="px-3 py-3 sticky top-0 text-left font-bold text-base"
                  >
                    {{ __('Read Notifications') }}
                  </th>
                </tr>
                <tr
                  v-for="notification in notifications.data"
                  :key="notification.id"
                >
                  <td class="px-3 py-3 whitespace-nowrap text-left text-sm">
                    <Link
                      v-if="notification.read_at"
                      :href="route('notifications.update', {notification})"
                      as="button"
                      method="put"
                      class="text-base leading-5 text-blue-600 hover:text-blue-700"
                    >
                      {{ __('Mark Unread') }}
                    </Link>
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-800">
                    {{ notification.created_at_for_humans }}
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                    <Badge :color="notification.read_at ? 'green' : 'yellow'">
                      {{ notification.read_at ? 'Read' : 'Unread' }}
                    </Badge>
                  </td>
                  <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                    <Link
                      :href="notification.data.link"
                      @start="markAsRead(notification.id, $event)"
                    >
                      {{ notification.data.message }}
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <Paginator
      :properties="notifications"
      class="mt-8"
    />
  </AppLayout>
</template>
