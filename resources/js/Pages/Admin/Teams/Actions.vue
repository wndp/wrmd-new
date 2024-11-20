<template>
  <AppLayout title="Accounts">
    <AccountHeader :account="account" />
    <Panel class="mt-8">
      <ol class="list-decimal list-inside space-y-1">
        <li v-if="canSpoof">
          <Link
            method="post"
            as="button"
            :href="route('accounts.spoof', account)"
            class="text-red-600"
          >
            Spoof this account
          </Link>
        </li>
        <li v-if="canSpoof">
          <Link
            :href="route('accounts.delete', account)"
            class="text-red-600"
          >
            Delete this account
          </Link>
        </li>
      </ol>
    </Panel>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import AccountHeader from './Partials/AccountHeader.vue';
import Panel from '@/Components/Panel.vue';

export default {
    components: {
        AppLayout,
        AccountHeader,
        Panel
    },
    props: {
        account: Object
    },
    computed: {
        canSpoof() {
            return this.can('spoofAccounts') && this.account.status === 'Active';
        }
    }
};
</script>
