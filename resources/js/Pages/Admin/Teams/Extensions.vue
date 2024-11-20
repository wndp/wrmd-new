<template>
  <AppLayout title="Accounts">
    <AccountHeader :account="account" />
    <ul
      role="list"
      class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 mt-8"
    >
      <ExtensionToggler
        v-for="extension in extensions"
        :key="extension.namespace"
        :extension="extension"
        :account="account"
      />
    </ul>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AccountHeader from './Partials/AccountHeader.vue';
import ExtensionToggler from '@/Components/ExtensionToggler.vue';

defineProps({
  account: {
    type: Object,
    required: true
  },
  extensions: {
    type: Array,
    required: true
  }
});
</script>
