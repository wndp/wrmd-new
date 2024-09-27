<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Alert from '@/Components/Alert.vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import Panel from '@/Components/Panel.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import PersonCard from '@/Components/FormCards/PersonCard.vue';
import IntakeCard from '@/Components/FormCards/IntakeCard.vue';
import ExamCard from '@/Components/FormCards/ExamCard.vue';
import DiagnosisCard from '@/Components/FormCards/DiagnosisCard.vue';
import OutcomeCard from '@/Components/FormCards/OutcomeCard.vue';
import CageCardCard from '@/Components/FormCards/CageCardCard.vue';
import { markRaw } from 'vue';
import mapValues from 'lodash/mapValues';
import isObject from 'lodash/isObject';
import isNil from 'lodash/isNil';
import omitBy from 'lodash/omitBy';
</script>

<script>
export default {
    props: {
      caseNumbers: {
        type: Array,
        required: true
      }
    },
    data() {
      return {
        howShouldTextValuesBeUpdated: [
          {
            value: null,
            label: ''
          },
          {
            value: 0,
            label: this.__('Delete existing text field values and write my new values in their place.')
          },
          {
            value: 1,
            label: this.__('Keep existing text field values and add my new values by comma separation.')
          }
        ],
        tabs: [
            // {title: 'Admission', components: [
            //     {component: markRaw(PersonCard), model: 'people', props: {affiliation: 'Rescuer'}},
            //     {component: markRaw(IntakeCard), model: 'intake', props: {}}
            // ]},
            // {title: 'Patient', components: [
            //     {component: markRaw(CageCardForm), model: 'cage_card', props: {}},
            //     {component: markRaw(ExamCard), model: 'exams', props: {}},
            //     {component: markRaw(DiagnosisCard), model: 'diagnosis', props: {}},
            //     {component: markRaw(OutcomeCard), model: 'outcome', props: {}}
            // ]},
            // {title: 'Treatment Log', components: [
            //     //{component: markRaw(TreatmentLog), model: 'treatment_log', props: {}},
            // ]},
            // {title: 'Location', components: [
            //     //{component: markRaw(CurrentLocation), model: 'current_location', props: {}},
            // ]}
        ],
        form: this.$inertia.form({
          how_should_text_values_be_updated: '',
          people: {},
          intake: {},
          cage_card: {},
          exams: {},
          diagnosis: {},
          outcome: {}
        })
      }
    },
    methods: {
      doBatchUpdate() {
        this.form.transform(data => {
          // remove empty values so that the post size is reduced
          return mapValues(data, val => isObject(val) ? omitBy(val, isNil) : val)
        }).put(this.route('patients.batch.update'))
      }
    }
};
</script>

<template>
  <AppLayout title="Batch Update">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 mb-8">
        {{ __('Batch Update') }}
      </h1>
    </template>
    <Alert>
      <strong class="text-bold">{{ __('Remember') }}</strong>
      <ol class="list-decimal list-inside marker:text-yellow-900">
        <li>{{ __('Batch updating is dangerous. Make sure you are updating the correct patients.') }}</li>
        <li>{{ __('Fill your values into the fields that you want to batch update.') }}</li>
        <li>{{ __('Blank values can not be batch updated.') }}</li>
        <li>{{ __('Choose how your text values should be updated.') }}</li>
      </ol>
    </Alert>
    <Alert color="blue" class="mt-2">
      <p>
        <strong class="text-bold">{{ __('These patients will be batch updated:') }}</strong> {{ caseNumbers.join(', ') }}
      </p>
    </Alert>
    <TabGroup :selected-index="1">
      <TabList class="border-b border-gray-200 mt-8">
        <nav
          class="-mb-px flex space-x-4"
          aria-label="Tabs"
        >
          <Tab
            v-for="tab in tabs"
            :key="tab.title"
            v-slot="{ selected }"
            as="template"
          >
            <button
              :class="[selected ? 'bg-white text-gray-700' : 'text-gray-500 hover:text-gray-700', 'flex items-center px-3 py-2 font-medium text-sm rounded-t-md']"
              :aria-current="selected ? 'page' : undefined"
            >
              {{ tab.title }}
            </button>
          </Tab>
        </nav>
      </TabList>
      <TabPanels>
        <TabPanel
          v-for="tab in tabs"
          :key="tab.title"
          class="space-y-4"
        >
          <component
            :is="component.component"
            v-for="component in tab.components"
            :key="component.model"
            v-model="form[component.model]"
            :can-submit="false"
            :enforce-required="false"
            v-bind="component.props"
          />
        </TabPanel>
      </TabPanels>
    </TabGroup>
    <Panel class="mt-8">
      <template #heading>
        {{ __('How Should Text Values be Updated?') }}
      </template>
      <div>
        <Select
          id="how_should_text_values_be_updated"
          v-model="form.how_should_text_values_be_updated"
          :options="howShouldTextValuesBeUpdated"
          required
          class="w-full"
        />
        <InputError
          :message="form.errors.how_should_text_values_be_updated"
          class="mt-1"
        />
      </div>
      <template #footing>
        <div class="flex items-center justify-start text-left">
          <PrimaryButton
            type="submit"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="doBatchUpdate"
          >
            {{ __('Batch Update My Patients') }}
          </PrimaryButton>
        </div>
      </template>
    </Panel>
  </AppLayout>
</template>
