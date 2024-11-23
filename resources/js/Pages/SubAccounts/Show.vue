<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import SubAccountTabs from './Partials/SubAccountTabs.vue';
import LeafletMap from '@/Components/Analytics/LeafletMap.vue';
import AnalyticNumber from '@/Components/Analytics/AnalyticNumber.vue';
import {__} from '@/Composables/Translate';

defineProps({
  subAccount: {
    type: Object,
    required: true
  },
  analyticFiltersForAllYears: {
    type: Object,
    required: true
  },
  analyticFiltersForThisYear: {
    type: Object,
    required: true
  },
  analyticFiltersForLastYear: {
    type: Object,
    required: true
  },
  analyticFiltersForThisWeek: {
    type: Object,
    required: true
  },
});
</script>

<template>
  <AppLayout title="Sub-Accounts">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ subAccount.name }}
      </h1>
    </template>
    <Link
      :href="route('sub_accounts.index')"
      class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
    >
      <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
      {{ __('Return to Sub-Accounts List') }}
    </Link>
    <SubAccountTabs
      :subAccount="subAccount"
      class="mt-4"
    />
    <div class="lg:grid lg:gap-8 lg:grid-cols-2 mt-8">
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Account Information
          </h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
          <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500">
                Date Created
              </dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ subAccount.created_at_for_humans }}
              </dd>
            </div>
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500">
                Location
              </dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ subAccount.locale }}
              </dd>
            </div>
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500">
                Contact
              </dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ subAccount.contact_name }}
              </dd>
            </div>
            <div class="sm:col-span-1">
              <dt class="text-sm font-medium text-gray-500">
                Email address
              </dt>
              <dd class="mt-1 text-sm text-gray-900">
                <a :href="`mailto:${subAccount.contact_email}`">{{ subAccount.contact_email }}</a>
              </dd>
            </div>
            <div
              v-if="subAccount.notes"
              class="sm:col-span-2"
            >
              <dt class="text-sm font-medium text-gray-500">
                Notes
              </dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ subAccount.notes }}
              </dd>
            </div>
          </dl>
          <!-- <Link
            method="post"
            as="button"
            :href="route('teams.spoof', subAccount)"
            class="mt-8 text-green-600"
          >
            Sign into this account
          </Link> -->
        </div>
      </div>
      <LeafletMap
        id="account"
        :title="subAccount.organization"
        :urlParams="{accountId: subAccount.id}"
        :height="400"
        class="mt-4 lg:mt-0 z-10"
      />
    </div>
    <div class="mt-5 grid grid-cols- md:grid-cols-3 gap-4">
      <AnalyticNumber
        id="patients-admitted"
        title="Total Patients"
        :urlParams="analyticFiltersForAllYears"
      />
      <AnalyticNumber
        id="patients-admitted"
        title="Patients This Year"
        :urlParams="analyticFiltersForThisYear"
      />
      <AnalyticNumber
        id="patients-admitted"
        title="Patients Last Year"
        :urlParams="analyticFiltersForLastYear"
      />
      <AnalyticNumber
        id="patients-admitted"
        title="Patients This Week"
        :urlParams="analyticFiltersForThisWeek"
      />
      <AnalyticNumber
        id="unrecognized-patients"
        title="Unrecognized Patients"
        :urlParams="analyticFiltersForAllYears"
      />
    </div>
  </AppLayout>
</template>
