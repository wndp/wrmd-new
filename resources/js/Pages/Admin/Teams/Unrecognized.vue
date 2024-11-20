<template>
    <AppLayout title="Accounts">
        <AccountHeader :account="account"/>
        <UnrecognizedAccountPatients :admissions="unrecognizedPatients.data" :account="account" class="mt-8" />
        <Paginator :properties="unrecognizedPatients" class="mt-8"/>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import AccountHeader from './Partials/AccountHeader.vue';
import Paginator from '@/Components/Paginator.vue';
import UnrecognizedAccountPatients from '@/Components/Unrecognized/UnrecognizedAccountPatients.vue';

export default {
    components: {
        AppLayout,
        AccountHeader,
        Paginator,
        UnrecognizedAccountPatients
    },
    props: {
        account: Object,
        unrecognizedPatients: Object
    },
};
</script>
