<script setup>
import { ref } from 'vue'
//import { usePage } from '@inertiajs/vue3'
import { PlusIcon } from '@heroicons/vue/24/solid';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import WashModal from './Partials/WashModal.vue';
import Panel from '@/Components/Panel.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import WashCard from './Partials/WashCard.vue';
import {__} from '@/Composables/Translate';

const showWashModal = ref(false);

defineProps({
  patient: {
    type: Object,
    required: true
  },
  washes: {
    type: Array,
    required: true
  },
  preTreatmentIsNoneId: {
    type: Number,
    required: true
  },
  washTypeIsInitialId: {
    type: Number,
    required: true
  },
});
</script>

<template>
  <PatientLayout title="Wash">
    <Panel v-if="washes.length === 0">
      <template #title>
        {{ __('No Wash Records Entered') }}
      </template>
      <template #content>
        <div class="col-span-3">
          <PrimaryButton
            class="flex items-center"
            @click="showWashModal = true"
          >
            <PlusIcon class="h-5 w-5 mr-2" /> Add Wash
          </PrimaryButton>
        </div>
      </template>
    </Panel>
    <div
      v-else
      class="space-y-4"
    >
      <PrimaryButton
        class="flex items-center"
        @click="showWashModal = true"
      >
        <PlusIcon class="h-5 w-5 mr-2" /> Add New Wash
      </PrimaryButton>
      <WashCard
        v-for="wash in washes"
        :key="wash.id"
        :wash="wash"
        :patientId="patient.id"
      />
    </div>
    <WashModal
      v-if="showWashModal"
      :show="true"
      :patientId="patient.id"
      :preTreatmentIsNoneId="preTreatmentIsNoneId"
      :washTypeIsInitialId="washTypeIsInitialId"
      @close="showWashModal = false"
    />
  </PatientLayout>
</template>
