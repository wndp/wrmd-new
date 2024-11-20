<template>
    <AppLayout title="Accounts">
        <AccountHeader :account="account"/>
        <MisidentifiedAccountPatients :admissions="misidentifiedPatients.data" class="mt-8" />
        <Paginator :properties="misidentifiedPatients" class="mt-8"/>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import AccountHeader from './Partials/AccountHeader.vue';
import Paginator from '@/Components/Paginator.vue';
import MisidentifiedAccountPatients from '@/Components/Unrecognized/MisidentifiedAccountPatients.vue';

export default {
    components: {
        AppLayout,
        AccountHeader,
        MisidentifiedAccountPatients,
        Paginator
    },
    props: {
        account: Object,
        misidentifiedPatients: Object
    },
};
</script>
