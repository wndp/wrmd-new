<script setup>
import {ref} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import IncidentMetaForm from './Partials/IncidentMetaForm.vue';
import IncidentDescriptionForm from './Partials/IncidentDescriptionForm.vue';
import IncidentCommunicationForm from './Partials/IncidentCommunicationForm.vue';
import IncidentResolutionForm from './Partials/IncidentResolutionForm.vue';
import { ArrowLongLeftIcon } from '@heroicons/vue/24/outline';
import PersonCard from '@/Components/FormCards/PersonCard.vue';
import SearchPeopleCard from '@/Components/FormCards/SearchPeopleCard.vue';
import ValidationErrors from '@/Components/FormElements/ValidationErrors.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import { PlusIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import { formatISO9075 } from 'date-fns';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';

const props = defineProps({
  statusOpenId: {
    type: Number,
    required: true
  }
});

const personTabIndex = ref(0);

const form = useForm({
  reported_at: formatISO9075(new Date()),
  occurred_at: null,
  recorded_by: usePage().props.auth.user.name,
  duration_of_call: null,
  suspected_species: null,
  number_of_animals: null,
  category_id: null,
  is_priority:  false,
  incident_status_id: props.statusOpenId,
  person_id: null,
  entity_id: '',
  organization: '',
  first_name: '',
  last_name: '',
  phone: '',
  alternate_phone: '',
  email: '',
  subdivision: '',
  city: '',
  address: '',
  county: '',
  postal_code: '',
  notes: '',
  no_solicitations: true,
  is_volunteer: false,
  is_member: false,

  incident_address: null,
  incident_city: null,
  incident_subdivision: null,
  incident_postal_code: null,
  description: null,

  communication_at: '',
  communication_by: '',
  communication: '',

  resolved_at: null,
  given_information: null,
  resolution: null,
})

const onChangeTab = (index) => personTabIndex.value = index;

const usePerson = (person) => {
    form.person = person;
    personTabIndex.value = 0;
};

const store = () => {
    form.transform(data => ({
      ...data,
      ...data.meta,
      ...data.details,
      ...data.outcome
    })).post(route('hotline.incident.store'));
};
</script>

<template>
  <AppLayout title="Hotline">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ __('New Hotline Incident') }}
      </h1>
    </template>
    <Link
      :href="route('hotline.open.index')"
      class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
    >
      <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
      {{ __('Return to Incidents List') }}
    </Link>
    <ValidationErrors class="mt-4" />
    <IncidentMetaForm
      :form="form"
      :canSubmit="false"
      class="mt-8"
    />
    <TabGroup
      id="upsert-person"
      :key="personTabIndex"
      as="div"
      :defaultIndex="personTabIndex"
      @change="onChangeTab"
    >
      <TabList
        class="border-b border-gray-200 mt-8"
      >
        <nav
          class="-mb-px flex space-x-4"
          aria-label="Tabs"
        >
          <Tab
            v-slot="{ selected }"
            as="template"
          >
            <button
              :class="[selected ? 'bg-white text-gray-700' : 'text-gray-500 hover:text-gray-700', 'flex items-center px-3 py-2 font-medium text-sm rounded-t-md']"
              :aria-current="selected ? 'page' : undefined"
            >
              <PlusIcon class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
              {{ __('Reporting Party') }}
            </button>
          </Tab>
          <Tab
            v-if="can(Abilities.COMPUTED_SEARCH_RESCUERS)"
            v-slot="{ selected }"
            as="template"
          >
            <button
              :class="[selected ? 'bg-white text-gray-700' : 'text-gray-500 hover:text-gray-700', 'flex items-center px-3 py-2 font-medium text-sm rounded-t-md']"
              :aria-current="selected ? 'page' : undefined"
            >
              <MagnifyingGlassIcon class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
              {{ __('Search') }}
            </button>
          </Tab>
        </nav>
      </TabList>
      <TabPanels>
        <TabPanel>
          <PersonCard
            :form="form"
            :canSubmit="false"
            affiliation="New Rescuer"
            class="rounded-t-none"
          />
          <div
            v-if="form.person_id"
            class="px-4 py-2 sm:px-6 bg-gray-50 rounded-b-lg shadow"
          >
            <div class="flex items-center justify-end text-right">
              <ActionMessage
                class="mr-3"
                :on="true"
              >
                <span class="text-red-600">
                  {{ __("You are using a known person. Any changed values will update the existing person's record.") }}
                </span>
              </ActionMessage>
              <DangerButton @click="form.person_id = null">
                {{ __("Don't Update Existing Person") }}
              </DangerButton>
            </div>
          </div>
        </TabPanel>
        <TabPanel v-if="can(Abilities.COMPUTED_SEARCH_RESCUERS)">
          <SearchPeopleCard @use="usePerson" />
        </TabPanel>
      </TabPanels>
    </TabGroup>
    <IncidentDescriptionForm
      :form="form"
      :canSubmit="false"
      class="mt-8"
    />
    <IncidentCommunicationForm
      :form="form"
      :canSubmit="false"
      class="mt-8"
    />
    <IncidentResolutionForm
      :form="form"
      :canSubmit="false"
      class="mt-8"
    />
    <PrimaryButton
      class="mt-8"
      :class="{ 'opacity-25': form.processing }"
      :disabled="form.processing"
      @click="store"
    >
      {{ __('Save New Hotline Incident') }}
    </PrimaryButton>
  </AppLayout>
</template>
