<script setup>
import {onMounted} from 'vue';
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PeopleTabs from '../Partials/PeopleTabs.vue';
import Alert from '@/Components/Alert.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import URI from 'urijs';
import LocalStorage from '@/Composables/LocalStorage';
import FormSection from '@/Components/FormElements/FormSection.vue';
import chunk from 'lodash/chunk';
import {__} from '@/Composables/Translate';

const localStorage = LocalStorage();

const form = useForm({
    fields: []
});

const fields = chunk([
    {name: __('Organization'), key: 'organization'},
    {name: __('First Name'), key: 'first_name'},
    {name: __('Last Name'), key: 'last_name'},
    {name: __('Phone Number'), key: 'phone'},
    {name: __('Alt. Phone Number'), key: 'alt_phone'},
    {name: __('Email'), key: 'email'},
    {name: __('Address'), key: 'address'},
    {name: __('City'), key: 'city'},
    {name: __('State'), key: 'subdivision'},
    {name: __('Postal Code'), key: 'postal_code'},
], 5);

onMounted(() => localStorage.store('peopleFilters', new URI().query(true)));

const search = () => form
  //.transform(data => data.fields.join(', '))
  .get(route('people.combine.matches'));
</script>

<template>
  <AppLayout title="Combine People">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ __('People') }}
      </h1>
    </template>
    <PeopleTabs class="mt-4" />
    <FormSection class="mt-8">
      <template #title>
        {{ __('Combine Duplicate People') }}
      </template>
      <template #description>
        <p>{{ __('If you have people who exist multiple times, you may use this tool to combine them together into one person.') }} {{ __('All patients, donations and any other related data will be reassigned to the combined person.') }}</p>
        <Alert
          color="red"
          class="mt-4"
        >
          <p class="text-sm">
            {{ __('Once you combine people, there is no way to change it back. This action CANNOT be undone. Make sure you are combining the correct people.') }}
          </p>
        </Alert>
        <h3 class="text-base leading-6 font-medium text-gray-800 mt-4">
          {{ __('Choose Fields to Search') }}
        </h3>
        <p class="mt-4">
          {{ __('First choose which fields you want to use to search for potential duplicate people. For example, selecting First Name and Last Name will search for all people with the same first name and same last name. By choosing multiple fields, you will increase the likelihood of finding true duplicate peoples.') }}
        </p>
      </template>
      <div class="col-span-2 space-y-2">
        <div
          v-for="field in fields[0]"
          :key="field.key"
          class="relative flex items-start"
        >
          <div class="flex items-center h-5">
            <Checkbox
              :id="`combine_using_${field.key}`"
              v-model="form.fields"
              :value="field.key"
            />
          </div>
          <div class="ml-3 text-sm">
            <label
              :for="`combine_using_${field.key}`"
              class="font-medium text-gray-700"
            >{{ __(field.name) }}</label>
          </div>
        </div>
      </div>
      <div class="col-span-2 space-y-2">
        <div
          v-for="field in fields[1]"
          :key="field.key"
          class="relative flex items-start"
        >
          <div class="flex items-center h-5">
            <Checkbox
              :id="field.name"
              v-model="form.fields"
              :value="field.key"
            />
          </div>
          <div class="ml-3 text-sm">
            <label
              :for="field.name"
              class="font-medium text-gray-700"
            >{{ __(field.name) }}</label>
          </div>
        </div>
      </div>
      <template #actions>
        <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="search"
        >
          {{ __('Search for Duplicate People') }}
        </PrimaryButton>
      </template>
    </FormSection>
  </AppLayout>
</template>
