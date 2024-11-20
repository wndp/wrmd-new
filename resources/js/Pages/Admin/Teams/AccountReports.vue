<template>
  <AppLayout title="Accounts">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-2xl font-semibold text-gray-900">
        Accounts
      </h1>
    </div>
    <AccountsHeader class="mt-5" />
    <ReportGroup
      :id="`reports-${accountReports.titleSlug}`"
      :title="accountReports.title"
      :reports="accountReports.reports"
      class="mt-8"
    />
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import AccountsHeader from './Partials/AccountsHeader.vue';
import ReportGroup from '@/Pages/Reports/Partials/ReportGroup.vue';

export default {
    components: {
        AppLayout,
        AccountsHeader,
        ReportGroup
    },
    props: {
        accountReports: Object,
    }
};
</script>
