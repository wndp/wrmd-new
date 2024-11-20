<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AdminNavigation from '../Partials/AdminNavigation.vue';
import ProfileForm from './Partials/ProfileForm.vue';
import TeamHeader from './Partials/TeamHeader.vue';

const props = defineProps({
  team: Object,
});
</script>

<template>
  <AppLayout title="Accounts">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <AdminNavigation class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <TeamHeader :team="team" />
        <ProfileForm
          :team="team"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>
