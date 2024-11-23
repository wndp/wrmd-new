<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AdminNavigation from '../Partials/AdminNavigation.vue';
import AnalyticNumber from '@/Components/Analytics/AnalyticNumber.vue';
import TeamHeader from './Partials/TeamHeader.vue';
import LeafletMap from '@/Components/Analytics/LeafletMap.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';

const route = inject('route');

const props = defineProps({
  team: Object,
  masterAccounts: Array,
  analyticFiltersForAllYears: Object,
  analyticFiltersForThisYear: Object,
  analyticFiltersForLastYear: Object,
  analyticFiltersForThisWeek: Object,
});

const form = useForm({
  master_account_id: props.team.master_account?.id
});

const saveMasterAccount = () => {
  form.put(route('teams.update.master-account', props.team));
}
</script>

<template>
  <AppLayout title="Accounts">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <AdminNavigation class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <TeamHeader :team="team" />
        <div class="md:grid md:gap-4 md:grid-cols-2 mt-8">
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Account Information
              </h3>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
              <dl class="grid grid-cols-2 gap-x-4 gap-y-8">
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">
                    Contact
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ team.contact_name }}
                  </dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">
                    Date Created
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ team.created_at_for_humans }}
                  </dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">
                    Email address
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <a :href="`mailto:${team.contact_email}`">{{ team.contact_email }}</a>
                  </dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">
                    Location
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ team.locale }}
                  </dd>
                </div>
                <div
                  v-if="team.notes"
                  class="sm:col-span-2"
                >
                  <dt class="text-sm font-medium text-gray-500">
                    Notes
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ team.notes }}
                  </dd>
                </div>
                <div
                  v-if="masterAccounts.length > 0 && ! team.is_master_account"
                  class="col-span-2"
                >
                  <dt class="text-sm font-bold text-gray-500">
                    Master Account
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <SelectInput
                      v-model="form.master_account_id"
                      name="master_account_id"
                      :options="masterAccounts"
                      hasBlankOption
                      class="mt-1 block w-full"
                    />
                    <PrimaryButton
                      class="mt-2"
                      @click="saveMasterAccount"
                    >
                      Update Master Account
                    </PrimaryButton>
                  </dd>
                </div>
              </dl>
            </div>
          </div>
          <LeafletMap
            id="account"
            title="WRMD Accounts"
            :urlParams="{teamId: team.id}"
            :height="400"
            class="mt-4 md:mt-0"
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
          <AnalyticNumber
            id="missidentified-patients"
            title="Missidentified Patients"
            :urlParams="analyticFiltersForAllYears"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
