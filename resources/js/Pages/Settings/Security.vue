<template>
  <AppLayout title="Security">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <SecuritySettingsForm :security="security" />
        <RemoteAccessForm
          :ip-address="ipAddress"
          :users="users"
          :remote-access="remoteAccess"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsAside from './Partials/SettingsAside.vue';
import SecuritySettingsForm from './Partials/SecuritySettingsForm.vue';
import RemoteAccessForm from './Partials/RemoteAccessForm.vue';

defineProps({
  ipAddress: String,
  users: Array,
  remoteAccess: Object,
  security: Object
});
</script>
