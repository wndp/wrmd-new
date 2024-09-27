<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MaintenanceAside from '../Partials/MaintenanceAside.vue';
import Panel from '@/Components/Panel.vue';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import { ArrowLongRightIcon } from '@heroicons/vue/24/outline';
</script>

<script>
export default {
    props: {
        whatImporting: {
          type: Array,
          required: true
        }
    },
    data() {
        return {
            form: this.$inertia.form({

            })
        };
    },
    methods: {
      nextStep() {

      }
    }
};
</script>

<template>
  <AppLayout title="Importing">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <MaintenanceAside class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
          <div class="bg-white py-6 px-4 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Wildlife Rehabilitation MD {{ __('Import') }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">{{ __('Using the Wildlife Rehabilitation MD Import tool, you can import patients and various related data into your database from other sources.') }}</p>
            <h4 class="mt-2 text-base font-medium text-gray-500">{{ __('There are five steps to import your data.') }}</h4>
            <ol class="mt-2 text-sm text-gray-500 list-decimal list-inside space-y-1">
              <li>{{ __('Tell us what you are importing and how you want it handled.') }}</li>
              <li>{{ __('Map your spreadsheet columns to the appropriate Wildlife Rehabilitation MD field.') }}</li>
              <li>{{ __('Translate your values to the appropriate Wildlife Rehabilitation MD select field values.') }}</li>
              <li>{{ __('Dry run the import to ensure your file will process safely.') }}</li>
              <li>{{ __("Start the import. That's it! Sit back and enjoy a coffee.") }}</li>
            </ol>
          </div>
        </div>
        <FormSection class="mt-8">
          <template #title>
            {{ __('Step 1: Import Declarations') }}
          </template>
          <template #description>
            <p>{{ __("First tell us what you're importing give us the URL to the Google Sheets with your data. Make sure to indicate if the first row in your file is a column header. You may also update existing records if a match is found using your provided data.") }}</p>
          </template>
          <div class="col-span-4 md:col-span-2">
            <Label for="what_importing">{{ __('What Are You Importing?') }}</label>
            <Select
              v-model="form.what_importing"
              name="what_importing"
              class="mt-1"
              :options="whatImporting"
            />
            <InputError
              :message="form.errors.what_importing"
              class="mt-2"
            />
          </div>
          <div class="col-span-4 md:col-span-2">
            <Label for="file">{{ __('Google Sheets URL') }}</label>
            <div>
              <Input
                v-model="form.file"
                name="file"
                class="mt-1"
              />
              <InputError
                :message="form.errors.file"
                class="mt-2"
              />
            </div>
          </div>
          <div class="col-span-4 md:col-span-2">
            <Toggle
              v-model="form.update_existing_records"
              name="update_existing_records"
              class="mt-1"
            >
              {{ __('Update existing records if a match is found.') }}
            </Toggle>
          </div>
          <template #actions>
            <PrimaryButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              class="flex items-center"
              @click="nextStep"
            >
              {{ __('Next Step') }}
              <ArrowLongRightIcon class="ml-2 h-5 w-5" />
            </PrimaryButton>
          </template>
        </FormSection>
      </div>
    </div>
  </AppLayout>
</template>
