<script setup>
import {computed} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import AdminNavigation from '../Partials/AdminNavigation.vue';
import TeamHeader from './Partials/TeamHeader.vue';
import Panel from '@/Components/Panel.vue';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
import {AccountStatus} from '@/Enums/AccountStatus';

const props = defineProps({
  team: Object
});

const canSpoof = computed(() => can(Abilities.COMPUTED_SPOOF_ACCOUNTS) && props.team.status === AccountStatus.ACTIVE);
</script>

<template>
  <AppLayout title="Accounts">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <AdminNavigation class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <TeamHeader :team="team" />
        <Panel class="mt-8">
          <template #content>
            <ol class="col-span-6 list-decimal list-inside space-y-1">
              <!-- <li v-if="canSpoof">
                <Link
                  method="post"
                  as="button"
                  :href="route('teams.spoof', team)"
                  class="text-red-600"
                >
                  Spoof this account
                </Link>
              </li> -->
              <li v-if="canSpoof">
                <Link
                  :href="route('teams.delete', team)"
                  class="text-red-600"
                >
                  Delete this account
                </Link>
              </li>
            </ol>
          </template>
        </Panel>
      </div>
    </div>
  </AppLayout>
</template>
