<script setup>
import {ref, computed} from 'vue';
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import PersonTabs from './Partials/PersonTabs.vue';
import PersonCard from '@/Components/FormCards/PersonCard.vue';
import DeletePersonForm from './Partials/DeletePersonForm.vue';
import Alert from '@/Components/Alert.vue';
import LocalStorage from '@/Composables/LocalStorage';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const localStorage = LocalStorage();

const props = defineProps({
  person: Object,
});

const form = useForm({
  custom_values: props.person.custom_values || {},
  entity_id: props.person.entity_id,
  organization: props.person.organization,
  first_name: props.person.first_name,
  last_name: props.person.last_name,
  phone: props.person.phone,
  alternate_phone: props.person.alternate_phone,
  email: props.person.email,
  subdivision: props.person.subdivision,
  city: props.person.city,
  address: props.person.address,
  county: props.person.county,
  postal_code: props.person.postal_code,
  notes: props.person.notes,
  no_solicitations: 'no_solicitations' in props.person ? props.person.no_solicitations : true,
  is_volunteer: props.person.is_volunteer,
  is_member: props.person.is_member,
});

const showDeletePersonForm = ref(
  can(Abilities.DELETE_PEOPLE) && ! (props.person.is_rescuer || props.person.is_reporting_party || props.person.is_donor)
);

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
    <PersonCard
      :form="form"
      :canSubmit="false"
      class="mt-4"
    />
    <DeletePersonForm
      v-if="showDeletePersonForm"
      :person="person"
      class="mt-4"
    />
    <Alert
      v-else
      class="mt-4"
    >
      {{ __('This person can not be deleted because they are either a rescuer, reporting party or a donor.') }}
    </Alert>
  </AppLayout>
</template>
