<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TeamHeader from './Partials/TeamHeader.vue';
import Paginator from '@/Components/Paginator.vue';
import AdminNavigation from '../Partials/AdminNavigation.vue';
import UnrecognizedAccountPatients from '@/Components/Unrecognized/UnrecognizedAccountPatients.vue';

defineProps({
  team: Object,
  unrecognizedPatients: Object
});
</script>

<template>
  <AppLayout title="Accounts">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <AdminNavigation class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <TeamHeader :team="team" />
        <UnrecognizedAccountPatients
          :admissions="unrecognizedPatients.data"
          :team="team"
          class="mt-8"
        />
        <Paginator
          :properties="unrecognizedPatients"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>
