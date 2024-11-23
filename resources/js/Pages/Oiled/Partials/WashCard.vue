<script setup>
import { ref } from 'vue'
import { TrashIcon, PencilIcon } from '@heroicons/vue/24/solid';
import WashModal from './WashModal.vue';
import DeleteWashModal from '../Partials/DeleteWashModal.vue';
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
  wash: {
    type: Object,
    default: null
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

const showWashModal = ref(false);
const showDeleteWashModal = ref(false);
</script>

<template>
  <Panel>
    <template #content>
      <div class="col-span-6 md:col-span-3 grid grid-cols-1 gap-y-4">
        <FormRow
          id="washed_at"
          :label="__('Date Washed')"
          inline
        >
          <TextOutput>{{ coalesce(wash.washed_at_for_humans) }}</TextOutput>
        </FormRow>
        <FormRow
          id="pre_treatment"
          :label="__('Pre-treatment / Duration')"
          inline
        >
          <div class="flex space-x-2">
            <TextOutput>{{ coalesce(wash.pre_treatment) }}</TextOutput>
            <span v-if="wash.pre_treatment_duration">
              {{ __('Minutes') }}: <TextOutput>{{ coalesce(wash.pre_treatment_duration) }}</TextOutput>
            </span>
          </div>
        </FormRow>
        <FormRow
          id="wash_type"
          :label="__('Wash Type / Duration')"
          inline
        >
          <div class="flex space-x-2">
            <TextOutput>{{ coalesce(wash.wash_type) }}</TextOutput>
            <span v-if="wash.wash_duration">
              <TextOutput>{{ coalesce(wash.wash_duration) }} {{ __('Minutes') }}</TextOutput>
            </span>
          </div>
        </FormRow>
        <FormRow
          id="detergent"
          :label="__('Detergent')"
          inline
        >
          <TextOutput>{{ coalesce(wash.Detergent) }}</TextOutput>
        </FormRow>
        <FormRow
          id="rinse_duration"
          :label="__('Rinse Duration')"
          inline
        >
          <TextOutput>
            {{ coalesce(wash.rinse_duration) }} <template v-if="wash.rinse_duration">
              {{ __('Minutes') }}
            </template>
          </TextOutput>
        </FormRow>
      </div>
      <div class="col-span-6 md:col-span-3 grid grid-cols-1 gap-y-4">
        <FormRow
          id="washer"
          :label="__('Washer(s)')"
          inline
        >
          <TextOutput>{{ coalesce(wash.washer) }}</TextOutput>
        </FormRow>
        <FormRow
          id="handler"
          :label="__('Handler(s)')"
          inline
        >
          <TextOutput>{{ coalesce(wash.handler) }}</TextOutput>
        </FormRow>
        <FormRow
          id="rinser"
          :label="__('Rinser(s)')"
          inline
        >
          <TextOutput>{{ coalesce(wash.rinser) }}</TextOutput>
        </FormRow>
        <FormRow
          id="dryer"
          :label="__('Dryer(s)')"
          inline
        >
          <TextOutput>{{ coalesce(wash.dryer) }}</TextOutput>
        </FormRow>
        <FormRow
          id="drying_method"
          :label="__('Drying Method / Duration')"
          inline
        >
          <div class="flex space-x-2">
            <TextOutput>{{ coalesce(wash.drying_method) }}</TextOutput>
            <span v-if="wash.drying_duration">
              <TextOutput>{{ coalesce(wash.drying_duration) }} {{ __('Minutes') }}</TextOutput>
            </span>
          </div>
        </FormRow>
      </div>
      <FormRow
        id="observations"
        :label="__('Observations')"
        class="col-span-6"
      >
        <TextOutput>{{ coalesce(wash.observations) }}</TextOutput>
      </FormRow>
    </template>
    <template #actions>
      <div class="flex items-center justify-between">
        <DangerButton
          class="flex items-center"
          @click="showDeleteWashModal = true"
        >
          <TrashIcon class="w-5 h-5 mr-2" />
          {{ __('Delete Wash Record') }}
        </DangerButton>
        <PrimaryButton
          class="flex items-center"
          @click="showWashModal = true"
        >
          <PencilIcon class="w-5 h-5 mr-2" />
          {{ __('Update Wash Record') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
  <WashModal
    v-if="showWashModal"
    :show="true"
    :wash="wash"
    :patientId="patientId"
    :preTreatmentIsNoneId="preTreatmentIsNoneId"
    :washTypeIsInitialId="washTypeIsInitialId"
    @close="showWashModal = false"
  />
  <DeleteWashModal
    v-if="showDeleteWashModal"
    :show="true"
    :wash="wash"
    :patientId="patientId"
    @close="showDeleteWashModal = false"
  />
</template>

