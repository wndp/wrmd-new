<script setup>
import {computed, ref} from 'vue';
import {usePage, useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CreatePatientIdentifiers from './Partials/CreatePatientIdentifiers.vue';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import Alert from '@/Components/Alert.vue';
import AlertAction from '@/Components/AlertAction.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import IntakeCard from '@/Components/FormCards/IntakeCard.vue';
import PersonCard from '@/Components/FormCards/PersonCard.vue';
import SearchPeopleCard from '@/Components/FormCards/SearchPeopleCard.vue';
import ValidationErrors from '@/Components/FormElements/ValidationErrors.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import { PlusIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import isNil from 'lodash/isNil';
import { formatISO9075 } from 'date-fns';
import {__} from '@/Composables/Translate';
import {can} from '@/Composables/Can';
import {Abilities} from '@/Enums/Abilities';
import axios from 'axios';
import {active} from '@/Composables/Extensions';
import {Extension} from '@/Enums/Extension';

const props = defineProps({
  incident: {
    type: Object,
    default: () => ({})
  }
});

const rescuerTabIndex = ref(0);

const form = useForm({
  incident_id: props.incident.id || null,
  custom_values: {},
  case_year: usePage().props.lastCaseId.year,
  admitted_at: formatISO9075(new Date()),
  taxon_id: '',
  common_name: props.incident.suspected_species || '',
  morph_id: '',
  cases_to_create: 1,
  reference_number: props.incident.incident_number || '',
  microchip_number: '',
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
  admitted_by: '',
  transported_by: '',
  found_at: props.incident.occurred_at || '',
  address_found: props.incident.location || '',
  city_found: props.incident.city || '',
  county_found: '',
  subdivision_found: props.incident.subdivision || '',
  postal_code_found: props.incident.postal_code || '',
  lat_found: '',
  lng_found: '',
  reason_for_admission: '',
  care_by_rescuer: '',
  notes_about_rescue: props.incident.description ? __('From Hotline incident :incidentNumber. :description', {
    incidentNumber: props.incident.incident_number,
    description: props.incident.description
  }) : '',
  donation_method_id: '',
  donation_value: '',
  donation_comments: '',
  rescuer: props.incident.reporting_party || {},
  action_after_store: 'return',
});

const currentYear = computed(() => new Date().getFullYear());
const notCurrentYear = computed(() => parseInt(form.case_year) !== currentYear.value);
const hasIncident = computed(() => ! isNil(props.incident.id));

const formatCaseNumber = (year, id) => {
    year = year.toString().substring(year.toString().length-2);
    return `${year}-${id}`;
};

const getNextCaseNumber = (caseYear) => {
    axios.get(route('admissions.year', {year: caseYear})).then(response => {
        nextCaseNumber.value = formatCaseNumber(
            response.data.year,
            response.data.last_case_id + 1
        );
    });
};

const onChangeTab = (index) => {
    rescuerTabIndex.value = index;
};

const usePerson = (person) => {
    form.rescuer = person;
    rescuerTabIndex.value = 0;
};

const store = () => {
    form.post(route('patients.store'), {
        onSuccess: () => {
          let year = form.case_year;
          form.reset();
          form.case_year = year;
          getNextCaseNumber(year);
        }
    });
};

const nextCaseNumber = ref(formatCaseNumber(usePage().props.lastCaseId.year, usePage().props.lastCaseId.id  +1));

const copyFromRescuer = () => {
  form.address_found = form.address,
  form.city_found = form.city
  form.county_found = form.county
  form.subdivision_found = form.subdivision
  form.postal_code_found = form.postal_code
};
</script>

<template>
  <AppLayout title="Admissions">
    <template #header>
      <div class="flex justify-between">
        <h1 class="text-2xl font-semibold text-gray-900 mb-2">
          {{ __('Admit New Patient') }}
        </h1>
        <Link
          v-if="active(Extension.QUICK_ADMIT)"
          :hre="route('patients.quick_admit.create')"
          class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150 mt-2"
        >
          {{ __('Go To Quick Admit') }}
        </Link>
        <!-- <div v-html="extensionNavigation('patient.create.header')" /> -->
      </div>
      <h2
        class="text-xl font-semibold mb-8"
        :class="[notCurrentYear ? 'text-red-700' : 'text-gray-800']"
      >
        {{ __('Next Case Number') }}: {{ nextCaseNumber }}
      </h2>
    </template>
    <Alert
      v-if="notCurrentYear"
      class="mt-2"
      color="red"
    >
      {{ __('You are admitting a patient into a previous year (:year)', {year: form.case_year}) }}. <strong class="font-bold">{{ __('Are you sure you want to do that?') }}</strong>
      <AlertAction
        color="red"
        @click="form.case_year = currentYear; getNextCaseNumber(currentYear)"
      >
        {{ __('No, I want the current year!') }}
      </AlertAction>
    </Alert>
    <Alert
      v-if="hasIncident"
      class="mt-2"
      color="red"
    >
      {{ __('You are admitting this patient from Hotline incident :incidentNumber.' ,{incidentNumber: incident.incident_number}) }} <strong class="font-bold">{{ __('Are you sure you want to do that?') }}</strong>
      <AlertAction
        color="red"
        @click="$inertia.get(route('patients.create'), {}, {preserveState: false})"
      >
        {{ __('No, admit the patient per normal!') }}
      </AlertAction>
    </Alert>
    <ValidationErrors class="mt-4" />
    <form @submit.prevent="store">
      <CreatePatientIdentifiers
        :form="form"
        class="mt-8"
        @case-year-change="getNextCaseNumber"
      />
      <TabGroup
        id="upsert-person"
        :key="rescuerTabIndex"
        as="div"
        :defaultIndex="rescuerTabIndex"
        @change="onChangeTab"
      >
        <TabList class="border-b border-gray-200 mt-8">
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
                {{ __('New Rescuer') }}
              </button>
            </Tab>
            <Tab
              v-if="can(Abilities.SEARCH_RESCUERS)"
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
              v-if="form.rescuer.id"
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
                <DangerButton @click="form.rescuer.id = null">
                  {{ __("Don't Update Existing Person") }}
                </DangerButton>
              </div>
            </div>
          </TabPanel>
          <TabPanel v-if="can(Abilities.SEARCH_RESCUERS)">
            <SearchPeopleCard @use="usePerson" />
          </TabPanel>
        </TabPanels>
      </TabGroup>

      <Panel class="mt-8">
        <template #title>
          {{ __('Donation') }}
        </template>
        <template #content>
          <FormRow
            id="donation_method_id"
            :label="__('Method')"
            class="col-span-6 md:col-span-3"
          >
            <SelectInput
              v-model="form.donation_method_id"
              name="donation_method_id"
              :options="$page.props.options.donationMethodsOptions"
              hasBlankOption
            />
            <InputError
              id="donation_method_id_error"
              class="mt-2"
              :message="form.errors?.donation?.method"
            />
          </FormRow>
          <FormRow
            id="donation_value"
            :label="__('Value')"
            class="col-span-6 md:col-span-3"
          >
            <TextInput
              v-model="form.donation_value"
              name="donation_value"
              type="number"
              min="0"
              step="0.01"
            />
            <InputError
              id="donation_value_error"
              class="mt-2"
              :message="form.errors?.donation?.value"
            />
          </FormRow>
          <FormRow
            id="donation_comments"
            :label="__('Comments')"
            class="col-span-6"
          >
            <TextInput
              v-model="form.donation_comments"
              name="donation_comments"
            />
            <InputError
              id="donation_comments_error"
              class="mt-2"
              :message="form.errors?.donation?.comments"
            />
          </FormRow>
        </template>
      </Panel>
      <button
        class="mt-8 text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150 mt-2"
        type="button"
        @click="copyFromRescuer"
      >
        {{ __('Copy Address From Rescuer') }}
      </button>
      <IntakeCard
        v-bind="$props"
        :form="form"
        :canSubmit="false"
        class="mt-1"
      />
      <Panel class="mt-8">
        <template #title>
          {{ __('What should happen after admitting this patient(s)?') }}
        </template>
        <template #content>
          <div class="col-span-6">
            <SelectInput
              v-model="form.action_after_store"
              name="action_after_store"
              :options="$page.props.options.actionsAfterStore"
              required
              class="w-full"
            />
            <InputError
              :message="form.errors.action_after_store"
              class="mt-1"
            />
          </div>
        </template>
        <template #actions>
          <div class="flex items-center justify-start text-left">
            <DangerButton
              v-if="notCurrentYear"
              type="submit"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              {{ __('Admit Patient into :year', {year: form.case_year}) }}
            </DangerButton>
            <PrimaryButton
              v-else
              type="submit"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              {{ __('Admit Patient into :year', {year: form.case_year}) }}
            </PrimaryButton>
            <ActionMessage
              :on="form.recentlySuccessful"
              class="ml-3"
            >
              {{ __('Saved.') }}
            </ActionMessage>
          </div>
        </template>
      </Panel>
    </form>
  </AppLayout>
</template>
