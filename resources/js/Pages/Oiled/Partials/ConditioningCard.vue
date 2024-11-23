<script setup>
import { ref } from 'vue'
import { TrashIcon, PencilIcon } from '@heroicons/vue/24/solid';
import ConditioningModal from './ConditioningModal.vue';
import DeleteConditioningModal from '../Partials/DeleteConditioningModal.vue';
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
  conditioning: {
    type: Object,
    default: null
  },
});

const showConditioningModal = ref(false);
const showDeleteConditioningModal = ref(false);
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
          <TextOutput>{{ coalesce(conditioning.evaluated_at_for_humans) }}</TextOutput>
        </FormRow>
        <FormRow
          id="examiner"
          :label="__('Examiner')"
          inline
        >
          <TextOutput>{{ coalesce(conditioning.examiner) }}</TextOutput>
        </FormRow>
        <FormRow
          id="buoyancy_id"
          :label="__('Buoyancy')"
          inline
        >
          <TextOutput>{{ coalesce(conditioning.buoyancy) }}</TextOutput>
        </FormRow>
        <FormRow
          id="hauled_out_id"
          :label="__('Hauled Out')"
          inline
        >
          <TextOutput>{{ coalesce(conditioning.hauled_out) }}</TextOutput>
        </FormRow>
      </div>
      <div class="col-span-6 md:col-span-3 grid grid-cols-1 gap-y-4">
        <FormRow
          id="preening_id"
          :label="__('Preening / Grooming')"
          inline
        >
          <TextOutput>{{ coalesce(conditioning.preening) }}</TextOutput>
        </FormRow>
        <FormRow
          id="self_feeding_id"
          :label="__('Self Feeding')"
          inline
        >
          <TextOutput>{{ coalesce(conditioning.self_feeding) }}</TextOutput>
        </FormRow>
        <FormRow
          id="flighted_id"
          :label="__('Flighted')"
          inline
        >
          <TextOutput>{{ coalesce(conditioning.flighted) }}</TextOutput>
        </FormRow>
      </div>
      <FormRow
        id="areas_wet_to_skin"
        :label="__('Area Wet To Skin')"
        inline
        class="col-span-6"
      >
        <div class="flex flex-wrap gap-x-6 gap-y-4">
          <template
            v-for="area in $page.props.options.oiledConditioningAreasWetToSkinOptions"
            :key="area.value"
          >
            <TextOutput v-if="conditioning.areas_wet_to_skin.includes(area.value)">
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
        <TextOutput>{{ coalesce(conditioning.comments) }}</TextOutput>
      </FormRow>
    </template>
    <template #actions>
      <div class="flex items-center justify-between">
        <DangerButton
          class="flex items-center"
          @click="showDeleteConditioningModal = true"
        >
          <TrashIcon class="w-5 h-5 mr-2" />
          {{ __('Delete Conditioning Evaluation') }}
        </DangerButton>
        <PrimaryButton
          class="flex items-center"
          @click="showConditioningModal = true"
        >
          <PencilIcon class="w-5 h-5 mr-2" />
          {{ __('Update Conditioning Evaluation') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
  <ConditioningModal
    v-if="showConditioningModal"
    :show="true"
    :conditioning="conditioning"
    :patientId="patientId"

    @close="showConditioningModal = false"
  />
  <DeleteConditioningModal
    v-if="showDeleteConditioningModal"
    :show="true"
    :conditioning="conditioning"
    :patientId="patientId"
    @close="showDeleteConditioningModal = false"
  />
</template>

