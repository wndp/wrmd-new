<script setup>
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import MediaUploader from '@/Components/Media/MediaUploader.vue';
import MediaList from '@/Components/Media/MediaList.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {MediaResource} from '@/Enums/MediaResource';
import {__} from '@/Composables/Translate';

defineProps({
  form: {
    type: Object,
    default: () => ({})
  },
  canSubmit: {
    type: Boolean,
    default: true
  },
  enforceRequired: {
    type: Boolean,
    default: true
  },
  patientId: {
    type: String,
    required: true
  },
  media: {
    type: Array,
    required: true
  }
});

const emit = defineEmits(['submitted']);

const doSubmit = () => emit('submitted');
</script>

<template>
  <Panel>
    <template #title>
      {{ __('Evidence') }}
    </template>
    <template #content>
      <div class="col-span-6 lg:col-span-3">
        <MediaUploader
          :resource="MediaResource.PATIENT"
          :resourceId="patientId"
          :postParams="{is_evidence: true}"
          @uploaded="router.reload({ only: ['media'] })"
        />
        <MediaList
          :media="media"
          :resource="MediaResource.PATIENT"
          :resourceId="patientId"
        />
      </div>
      <div class="col-span-6 lg:col-span-3 space-y-4">
        <FormRow
          id="evidence_collected"
          :required="true"
        >
          <div class="flex flex-wrap gap-x-4 gap-y-3 sapce-between">
            <div
              v-for="evidence in $page.props.options.oiledProcessingEvidencesOptions"
              :key="evidence.value"
              class="flex items-start"
            >
              <div class="flex items-center">
                <Checkbox
                  :id="evidence.value"
                  v-model="form.evidence_collected"
                  name="evidence_collected[]"
                  :value="evidence.value"
                />
                <InputLabel
                  :for="evidence.value"
                  class="ml-2 font-normal whitespace-nowrap"
                >
                  {{ evidence.label }}
                </InputLabel>
              </div>
            </div>
          </div>
          <InputError
            :message="form.errors.evidence_collected"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="evidence_collected_by"
          :label="__('Evidence Collected By')"
          :required="true"
        >
          <TextInput
            v-model="form.evidence_collected_by"
            name="evidence_collected_by"
          />
          <InputError
            :message="form.errors.evidence_collected_by"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="processed_at"
          :label="__('Date Processed')"
          :required="true"
        >
          <DatePicker
            id="processed_at"
            v-model="form.processed_at"
            time
          />
          <InputError
            :message="form.errors.processed_at"
            class="mt-2"
          />
        </FormRow>
      </div>
    </template>
    <template
      v-if="canSubmit"
      #actions
    >
      <div class="flex items-center justify-end text-right">
        <ActionMessage
          :on="form.isDirty"
          class="mr-3"
        >
          <span class="text-red-600">{{ __('There are unsaved changes.') }}</span>
        </ActionMessage>
        <ActionMessage
          :on="form.recentlySuccessful"
          class="mr-3"
        >
          {{ __('Saved.') }}
        </ActionMessage>
        <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="doSubmit"
        >
          {{ __('Update Evidence') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
