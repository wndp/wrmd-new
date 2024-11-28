<script setup>
import { ref } from 'vue'
import { TrashIcon, PencilIcon } from '@heroicons/vue/24/solid';
import WaterproofingAssessmentModal from './WaterproofingAssessmentModal.vue';
import DeleteWaterproofingAssessmentModal from '../Partials/DeleteWaterproofingAssessmentModal.vue';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextOutput from '@/Components/FormElements/TextOutput.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {coalesce} from '@/Composables/DisplayPlaceholder.ts';
import {__} from '@/Composables/Translate';

defineProps({
  patientId: {
    type: String,
    required: true
  },
  assessment: {
    type: Object,
    default: null
  },
});

const showWaterproofingAssessmentModal = ref(false);
const showDeleteWaterproofingAssessmentModal = ref(false);
</script>

<template>
  <Panel>
    <template #content>
      <div class="col-span-6 md:col-span-3 grid grid-cols-1 gap-y-4">
        <FormRow
          id="evaluated_at"
          :label="__('Date Evaluated')"
          inline
        >
          <TextOutput>{{ coalesce(assessment.evaluated_at_for_humans) }}</TextOutput>
        </FormRow>
        <FormRow
          id="examiner"
          :label="__('Examiner')"
          inline
        >
          <TextOutput>{{ coalesce(assessment.examiner) }}</TextOutput>
        </FormRow>
        <FormRow
          id="buoyancy_id"
          :label="__('Buoyancy')"
          inline
        >
          <TextOutput>{{ coalesce(assessment.buoyancy) }}</TextOutput>
        </FormRow>
        <FormRow
          id="hauled_out_id"
          :label="__('Hauled Out')"
          inline
        >
          <TextOutput>{{ coalesce(assessment.hauled_out) }}</TextOutput>
        </FormRow>
      </div>
      <div class="col-span-6 md:col-span-3 grid grid-cols-1 gap-y-4">
        <FormRow
          id="preening_id"
          :label="__('Preening / Grooming')"
          inline
        >
          <TextOutput>{{ coalesce(assessment.preening) }}</TextOutput>
        </FormRow>
      </div>
      <FormRow
        id="areas_wet_to_skin"
        :label="__('Areas Wet To Skin')"
        inline
        class="col-span-6"
      >
        <div class="flex flex-wrap gap-x-6 gap-y-4">
          <template
            v-for="area in $page.props.options.oiledConditioningAreasWetToSkinOptions"
            :key="area.value"
          >
            <TextOutput v-if="assessment.areas_wet_to_skin?.includes(area.value)">
              {{ area.label }}
            </TextOutput>
          </template>
        </div>
      </FormRow>
      <FormRow
        id="areas_surface_wet"
        :label="__('Areas Surface Wet')"
        inline
        class="col-span-6"
      >
        <div class="flex flex-wrap gap-x-6 gap-y-4">
          <template
            v-for="area in $page.props.options.oiledConditioningAreasWetToSkinOptions"
            :key="area.value"
          >
            <TextOutput v-if="assessment.areas_surface_wet?.includes(area.value)">
              {{ area.label }}
            </TextOutput>
          </template>
        </div>
      </FormRow>
      <FormRow
        id="comments"
        :label="__('Comments')"
        class="col-span-6"
      >
        <TextOutput>{{ coalesce(assessment.comments) }}</TextOutput>
      </FormRow>
    </template>
    <template #actions>
      <div class="flex items-center justify-between">
        <button
          class="flex items-center text-red-500"
          @click="showDeleteWaterproofingAssessmentModal = true"
        >
          <TrashIcon class="w-5 h-5 mr-2" />
          {{ __('Delete Waterproofing Assessment') }}
        </button>
        <PrimaryButton
          class="flex items-center"
          @click="showWaterproofingAssessmentModal = true"
        >
          <PencilIcon class="w-5 h-5 mr-2" />
          {{ __('Update Waterproofing Assessment') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
  <WaterproofingAssessmentModal
    v-if="showWaterproofingAssessmentModal"
    :show="true"
    :assessment="assessment"
    :patientId="patientId"

    @close="showWaterproofingAssessmentModal = false"
  />
  <DeleteWaterproofingAssessmentModal
    v-if="showDeleteWaterproofingAssessmentModal"
    :show="true"
    :assessment="assessment"
    :patientId="patientId"
    @close="showDeleteWaterproofingAssessmentModal = false"
  />
</template>

