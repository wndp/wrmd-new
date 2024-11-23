<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TeamHeader from './Partials/TeamHeader.vue';
import Paginator from '@/Components/Paginator.vue';
import AdminNavigation from '../Partials/AdminNavigation.vue';
import MisidentifiedAccountPatients from '@/Components/Unrecognized/MisidentifiedAccountPatients.vue';

defineProps({
  team: Object,
  misidentifiedPatients: Object
});
</script>

<template>
  <AppLayout title="Accounts">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <AdminNavigation class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <TeamHeader :team="team" />
        <MisidentifiedAccountPatients
          :admissions="misidentifiedPatients.data"
          class="mt-8"
        />
        <Paginator
          :properties="misidentifiedPatients"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>
