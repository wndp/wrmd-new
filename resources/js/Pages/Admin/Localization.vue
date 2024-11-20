<template>
  <AppLayout title="Localization">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-2xl font-semibold text-gray-900">
        Localization
      </h1>
    </div>
    <div class="sm:grid sm:grid-cols-2 sm:gap-4 mt-5">
      <Panel>
        <template #heading>
          Countries
        </template>
        <ol class="list-decimal list-inside space-y-1">
          <li
            v-for="country in $page.props.options.countries"
            :key="country"
          >
            ({{ country.value }}) {{ country.label }}
          </li>
        </ol>
      </Panel>
      <Panel class="mt-4 sm:mt-0">
        <template #heading>
          Agency Reports
        </template>
        <ol class="list-decimal list-inside space-y-1">
          <li
            v-for="locales in annualReports"
            :key="locales"
          >
            {{ locales }}
          </li>
        </ol>
      </Panel>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Panel.vue';

defineProps({
  annualReports: {
    type: Object,
    required: true
  }
});
</script>
