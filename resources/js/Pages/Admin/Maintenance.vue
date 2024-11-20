<template>
  <AppLayout title="Admin Maintenance">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-2xl font-semibold text-gray-900">
        Admin Maintenance
      </h1>
    </div>
    <div class="grid grid-cols- md:grid-cols-3 gap-4 mt-5">
      <AnalyticNumber
        id="maintenance-to-do"
        title="Media to Migrate"
        :params="{
          &quot;table&quot;: &quot;attachments&quot;,
          &quot;where&quot;: {&quot;is_migrated&quot;: false}
        }"
      />
      <AnalyticNumber
        id="maintenance-to-do"
        title="Patients to Geocode"
        :params="{
          &quot;table&quot;: &quot;patients&quot;,
          &quot;where&quot;: {&quot;coordinates_found&quot;: null}
        }"
      />
      <AnalyticNumber
        id="maintenance-to-do"
        title="Hotlines to Geocode"
        :params="{
          &quot;table&quot;: &quot;hotline_incidents&quot;,
          &quot;where&quot;: {&quot;coordinates&quot;: null}
        }"
      />
      <AnalyticNumber
        id="all-unrecognized-patients"
        title="All Unrecognized Patients"
      />
      <AnalyticNumber
        id="all-missidentified-patients"
        title="All Missidentified Patients"
      />
    </div>
    <Panel class="mt-4">
      <template #heading>
        Maintenance Scripts
      </template>
      <ol class="list-decimal list-inside space-y-1">
        <li>
          <Link
            method="post"
            as="button"
            :href="route('admin.maintenance.dispatch', 'unSpoof')"
            class="text-blue-600"
          >
            Un-spoof users from all accounts.
          </Link>
        </li>
        <li>
          <Link
            method="post"
            as="button"
            :href="route('admin.maintenance.dispatch', 'unrecognized')"
            class="text-blue-600"
          >
            Attempt to identify the unrecognized species.
          </Link>
        </li>
        <li>
          <Link
            method="post"
            as="button"
            :href="route('admin.maintenance.dispatch', 'misidentified')"
            class="text-blue-600"
          >
            Attempt to identify the misidentified species.
          </Link>
        </li>
        <li>
          <Link
            method="post"
            as="button"
            :href="route('admin.maintenance.dispatch', 'geocode')"
            class="text-blue-600"
          >
            Geocode addresses.
          </Link>
        </li>
        <li>
          <Link
            method="post"
            as="button"
            :href="route('admin.maintenance.dispatch', 'unlock')"
            class="text-blue-600"
          >
            Unlock patients that are locked.
          </Link>
        </li>
      </ol>
    </Panel>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AnalyticNumber from '@/Components/Analytics/AnalyticNumber.vue';
import Panel from '@/Components/Panel.vue';
</script>
