<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Alert from '@/Components/Alert.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import FormSection from '@/Components/FormElements/FormSection.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import ClassificationTree from './Partials/ClassificationTree.vue';
import SettingsAside from './Partials/SettingsAside.vue';
import {__} from '@/Composables/Translate';

const route = inject('route');

const props =  defineProps({
  showTags: Boolean
});

const form = useForm({
  showTags: props.showTags,
});

const tabs = [
  {
    key: 'CircumstancesOfAdmission',
    name: 'Circumstances Of Admission',
    description: 'Why did the rescuer bring me this animal?'
  },
  {
    key: 'ClinicalClassifications',
    name: 'Clinical Classifications',
    description: 'What are the physical exam findings by body system?'
  },
  {
    key: 'CategorizationOfClinicalSigns',
    name: 'Categorization Of Clinical Signs',
    description: 'What caused the abnormal physical exam findings (Clinical Classifications)?'
  }
];

const updateClassifications = () => {
  form.put(route('classification-tagging.update'), {
    preserveScroll: true
  });
};
</script>

<template>
  <AppLayout title="Classifications">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <SettingsAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <FormSection>
          <template #title>
            {{ __('Classification Tagging') }}
          </template>
          <template #description>
            <div class="space-y-4">
              <p>{{ __('Wildlife Rehabilitation MD uses a hierarchical classification system to automatically tag your patients with standardized terminology into three major categories:') }}</p>

              <p>
                <b>1. {{ __('Circumstances of Admission (ex: Cat interaction, Vehicle collision, ...)') }}</b>
                <br>&mdash; {{ __('Why did the rescuer bring me this animal?') }}
              </p>

              <p>
                <b>2. {{ __('Clinical Classifications (ex: Physical injury, Neurologic disease, ...)') }}</b>
                <br>&mdash; {{ __('What are the physical exam findings by body system?') }}
              </p>

              <p>
                <b>3. {{ __('Categorization Of Clinical Signs (ex: Trauma, External parasites, ...)') }}</b>
                <br>&mdash; {{ __('What caused the abnormal physical exam findings (Clinical Classifications)?') }}
              </p>

              <p>{{ __('The terms in each category are meant to encompass, at a baseline level, the most common situations encountered in wildlife rehabilitation. For example, the Clinical Classifications category only classifies to the body systems, not to each individual organ or body part.') }}</p>

              <p>{{ __('Some terms will be automatically tagged on your patients by WRMD. WRMD will read data already recorded on the patient to predict which tags best fit.') }}</p>

              <p>{{ __('You may use the terms in the classification tagging fields as-is or you may also add custom terms for specific classifications that your hospital encounters. Your custom terms must be assigned to an existing root or leaf node of the terminology hierarchy. For example, you could use the Circumstances of Admission term "Cat interaction" or add the custom term "Feral cat" under Cat interaction in order to track that detail.') }}</p>
            </div>
          </template>
          <div class="col-span-4">
            <InputLabel for="first-name">{{ __('Show Classification Fields?') }}</InputLabel>
            <div class="mt-2">
              <Toggle v-model="form.showTags" dusk="showTags" />
            </div>
          </div>
          <template #actions>
            <ActionMessage
              :on="form.recentlySuccessful"
              class="mr-3"
            >
              {{ __('Saved.') }}
            </ActionMessage>
            <PrimaryButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="updateClassifications"
            >
              {{ __('Update Classifications Settings') }}
            </PrimaryButton>
          </template>
        </FormSection>
        <template v-if="showTags">
          <div class="mt-8">
            <Alert>
              <div class="prose max-w-none">
                <h6>{{ __('WARNING') }}</h6>
                <p>If you <b>edit</b> a custom term, all saved terms for your patients with that term will update from the old term value to the new term value.</p>

                <p>If you <b>delete</b> a custom term, all saved terms for your patients with that term will be permanently deleted as well. This <b>CAN NOT</b> be undone. If you need to move an existing custom term to a different location in the terminology hierarchy then please <a href="">ask us for help</a>.</p>
              </div>
            </Alert>
          </div>
          <div class="mt-8">
            <TabGroup>
              <TabList class="border-b border-gray-200">
                <nav
                  class="-mb-px flex space-x-8"
                  aria-label="Tabs"
                >
                  <Tab
                    v-for="tab in tabs"
                    :key="tab.key"
                    v-slot="{ selected }"
                    as="template"
                  >
                    <button
                      :class="[selected ? 'bg-white text-gray-700' : 'text-gray-500 hover:text-gray-700', 'flex items-center px-3 py-2 font-medium text-lg rounded-t-md']"
                      :aria-current="selected ? 'page' : undefined"
                    >
                      {{ tab.name }}
                    </button>
                  </Tab>
                </nav>
              </TabList>
              <TabPanels>
                <TabPanel
                  v-for="tab in tabs"
                  :key="tab.key"
                >
                  <div class="bg-white overflow-hidden shadow rounded-b-lg divide-y divide-gray-200">
                    <div class="px-4 py-5 sm:p-6">
                      <h4 class="text-lg text-gray-600 mb-4">
                        {{ tab.description }}
                      </h4>
                      <ClassificationTree :category="tab.key" />
                    </div>
                  </div>
                </TabPanel>
              </TabPanels>
            </TabGroup>
          </div>
        </template>
      </div>
    </div>
  </AppLayout>
</template>
