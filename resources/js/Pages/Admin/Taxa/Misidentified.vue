<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {useForm} from '@inertiajs/vue3';
import AdminNavigation from '../Partials/AdminNavigation.vue';
import TaxaHeader from './Partials/TaxaHeader.vue';
import Paginator from '@/Components/Paginator.vue';
import MisidentifiedPatients from '@/Components/Unrecognized/MisidentifiedPatients.vue';
import FormSection from '@/Components/FormElements/FormSection.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import URI from 'urijs';

defineProps({
  misidentifiedPatients: Object,
});

const form = useForm({
    common_name: new URI().query(true).common_name
});

const filterPatients = () => {
    form.get(route('taxa.unrecognized.index'), {
        preserveScroll: true,
        only: ['unrecognizedPatients']
    });
};
</script>

<template>
  <AppLayout title="Admin">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <AdminNavigation class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <TaxaHeader />
        <FormSection
          class="mt-8"
          @submitted="filterPatients"
        >
          <div class="col-span-4 flex items-center">
            <InputLabel
              for="common_name"
              class="whitespace-nowrap"
            >
              Common Name
            </InputLabel>
            <TextInput
              v-model="form.common_name"
              class="mt-1 ml-4"
            />
          </div>
          <template #actions>
            <PrimaryButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="filterPatients"
            >
              Search
            </PrimaryButton>
          </template>
        </FormSection>
        <MisidentifiedPatients
          :admissions="misidentifiedPatients.data"
          class="mt-8"
        />
        <Paginator
          :properties="misidentifiedPatients"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>
