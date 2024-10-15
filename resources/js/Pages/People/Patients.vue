<script setup>
import {computed} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PersonTabs from './Partials/PersonTabs.vue';
import LocalStorage from '@/Composables/LocalStorage';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import PatientsList from '@/Pages/Patients/Partials/PatientsList.vue';
import Paginator from '@/Components/Paginator.vue';
import {__} from '@/Composables/Translate';

const localStorage = LocalStorage();

defineProps({
  person: Object,
  listFigures: Object
});

const uri = computed(() => localStorage.get('peopleFilters'));
</script>

<template>
  <AppLayout :title="person.identifier">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ person.identifier }}
      </h1>
      <Link
        :href="route('people.rescuers.index', uri)"
        class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150 mb-8"
      >
        <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
        {{ __('Return to People') }}
      </Link>
    </template>
    <PersonTabs :person="person" />
    <PatientsList
      :data="listFigures"
      class="mt-4"
    />
    <Paginator
      v-if="listFigures.rows.first_page_url"
      :properties="listFigures.rows"
      class="mt-8"
    />
  </AppLayout>
</template>
