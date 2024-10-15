<script setup>
import {onMounted} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PeopleTabs from './Partials/PeopleTabs.vue';
import PeopleListHeader from './Partials/PeopleListHeader.vue';
import PeopleList from './Partials/PeopleList.vue';
import Alert from '@/Components/Alert.vue';
import Paginator from '@/Components/Paginator.vue';
import URI from 'urijs';
import LocalStorage from '@/Composables/LocalStorage';
import {__} from '@/Composables/Translate';

const localStorage = LocalStorage();

defineProps({
  group: String,
  people: Object
});

onMounted(() => localStorage.store('peopleFilters', new URI().query(true)));
</script>

<template>
  <AppLayout title="Rescuers">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ __('People') }}
      </h1>
    </template>
    <Alert
      dismissible
      class="mt-4"
    >
      {{ __('Below is a list of all the people stored in your account. Your people might be listed in more than one group (rescuers, donors, volunteers ...) and can be exported for use in your own constituent database.') }}
    </Alert>
    <PeopleTabs class="mt-4" />
    <PeopleListHeader
      :people="people"
      :group="group"
      class="mt-8"
    />
    <PeopleList
      :people="people"
      class="mt-8"
    />
    <Paginator
      :properties="people"
      class="mt-8"
    />
  </AppLayout>
</template>
