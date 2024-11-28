<script setup>
import {ref} from 'vue'
import { PlusIcon } from '@heroicons/vue/24/solid';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import Panel from '@/Components/Panel.vue';
import WaterproofingAssessmentModal from './Partials/WaterproofingAssessmentModal.vue';
import WaterproofingAssessmentCard from './Partials/WaterproofingAssessmentCard.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

defineProps({
  patient: {
    type: Object,
    required: true
  },
  assessments: {
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

const showWaterproofingAssessmentModal = ref(false);
</script>

<template>
  <PatientLayout title="Conditioning">
    <Panel v-if="assessments.length === 0">
      <template #title>
        {{ __('No Waterproofing Assessments Entered') }}
      </template>
      <template #content>
        <div class="col-span-3">
          <PrimaryButton
            class="flex items-center"
            @click="showWaterproofingAssessmentModal = true"
          >
            <PlusIcon class="h-5 w-5 mr-2" /> {{ __('Add New Waterproofing Assessment') }}
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
        @click="showWaterproofingAssessmentModal = true"
      >
        <PlusIcon class="h-5 w-5 mr-2" /> {{ __('Add New Waterproofing Assessment') }}
      </PrimaryButton>
      <WaterproofingAssessmentCard
        v-for="assessment in assessments"
        :key="assessment.id"
        :assessment="assessment"
        :patientId="patient.id"
      />
    </div>
    <WaterproofingAssessmentModal
      v-if="showWaterproofingAssessmentModal"
      :show="true"
      :patientId="patient.id"
      @close="showWaterproofingAssessmentModal = false"
    />
  </PatientLayout>
</template>
