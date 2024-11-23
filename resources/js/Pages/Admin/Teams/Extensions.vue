<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TeamHeader from './Partials/TeamHeader.vue';
import AdminNavigation from '../Partials/AdminNavigation.vue';
import ExtensionToggler from '@/Components/ExtensionToggler.vue';

defineProps({
  team: {
    type: Object,
    required: true
  },
  extensions: {
    type: Array,
    required: true
  }
});
</script>

<template>
  <AppLayout title="Accounts">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <AdminNavigation class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <TeamHeader :team="team" />
        <ul
          role="list"
          class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2 mt-8"
        >
          <ExtensionToggler
            v-for="extension in extensions"
            :key="extension.namespace"
            :extension="extension"
            :team="team"
          />
        </ul>
      </div>
    </div>
  </AppLayout>
</template>
