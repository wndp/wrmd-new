<script setup>
import {ref} from 'vue'
import { PlusIcon } from '@heroicons/vue/24/solid';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import Panel from '@/Components/Panel.vue';
import ConditioningModal from './Partials/ConditioningModal.vue';
import ConditioningCard from './Partials/ConditioningCard.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

defineProps({
  patient: {
    type: Object,
    required: true
  },
  conditionings: {
    type: Array,
    required: true
  },
  // preTreatmentIsNoneId: {
  //   type: Number,
  //   required: true
  // },
  // washTypeIsInitialId: {
  //   type: Number,
  //   required: true
  // },
});

const showConditioningModal = ref(false);
</script>

<template>
  <PatientLayout title="Conditioning">
    <Panel v-if="conditionings.length === 0">
      <template #title>
        {{ __('No Conditioning Records Entered') }}
      </template>
      <template #content>
        <div class="col-span-3">
          <PrimaryButton
            class="flex items-center"
            @click="showConditioningModal = true"
          >
            <PlusIcon class="h-5 w-5 mr-2" /> Add Conditioning
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
        @click="showConditioningModal = true"
      >
        <PlusIcon class="h-5 w-5 mr-2" /> Add New Conditioning Evaluation
      </PrimaryButton>
      <ConditioningCard
        v-for="conditioning in conditionings"
        :key="conditioning.id"
        :conditioning="conditioning"
        :patientId="patient.id"
      />
    </div>
    <ConditioningModal
      v-if="showConditioningModal"
      :show="true"
      :patientId="patient.id"
      @close="showConditioningModal = false"
    />
  </PatientLayout>
</template>
