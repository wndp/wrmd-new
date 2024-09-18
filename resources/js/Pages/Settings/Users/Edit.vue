<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsAside from '../Partials/SettingsAside.vue';
import UpdateUserForm from './Partials/UpdateUserForm.vue';
import UserAuthorizationsForm from './Partials/UserAuthorizationsForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';

defineProps({
  user: Object,
  abilities: Array,
  allowedAbilities: Array,
  forbiddenAbilities: Array,
  unAllowedAbilities: Array
});
</script>

<template>
  <AppLayout title="Users">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <UpdateUserForm
          :user="user"
        />
        <UserAuthorizationsForm
          :user="user"
          :abilities="abilities"
          :allowedAbilities="allowedAbilities"
          :forbiddenAbilities="forbiddenAbilities"
          :unAllowedAbilities="unAllowedAbilities"
          class="mt-8"
        />
        <DeleteUserForm
          v-if="user.id !== $page.props.auth.user.id"
          :user="user"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>
