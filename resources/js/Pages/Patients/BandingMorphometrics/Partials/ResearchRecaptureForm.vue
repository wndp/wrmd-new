<script setup>
import {useForm} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  patientId: {
    type: String,
    required: true
  },
  banding: {
    type: Object,
    default: () => ({})
  },
  enforceRequired: {
    type: Boolean,
    default: true
  }
});

const form = useForm({
  recaptured_at: props.banding?.recaptured_at,
  recapture_disposition_id: props.banding?.recapture_disposition_id,
  present_condition_id: props.banding?.present_condition_id,
  how_present_condition_id: props.banding?.how_present_condition_id,
});

const save = () => {
  form.put(route('patients.banding-morphometrics.recapture.update', {
    patient: props.patientId
  }), {
    preserveScroll: true,
    //onError: () => this.stopAutoSave()
  });
};
</script>

<template>
  <Panel>
    <template #title>
      {{ __('Recapture') }}
    </template>
    <template #content>
      <FormRow
        id="recaptured_at"
        :label="__('Recapture Date')"
        :required="enforceRequired"
        class="col-span-6 md:col-span-2"
      >
        <DatePicker
          id="recaptured_at"
          v-model="form.recaptured_at"
          :required="enforceRequired"
        />
        <InputError :message="form.errors.recaptured_at" />
      </FormRow>
      <FormRow
        id="recapture_disposition_id"
        :label="__('Recapture Disposition')"
        class="col-span-6 md:col-span-2 md:col-start-1"
      >
        <SelectInput
          v-model="form.recapture_disposition_id"
          name="recapture_disposition_id"
          :options="$page.props.options.bandingRecaptureDispositionCodesOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.recapture_disposition_id" />
      </FormRow>
      <FormRow
        id="present_condition_id"
        :label="__('Present Condition')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.present_condition_id"
          name="present_condition_id"
          :options="$page.props.options.bandingPresentConditionCodesOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.present_condition_id" />
      </FormRow>
      <FormRow
        id="how_present_condition_id"
        :label="__('Present Condition')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.how_present_condition_id"
          name="how_present_condition_id"
          :options="$page.props.options.bandingHowPresentConditionCodesOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.how_present_condition_id" />
      </FormRow>
    </template>
    <template #actions>
      <div class="flex items-center justify-end text-right">
        <ActionMessage
          :on="form.isDirty"
          class="mr-3"
        >
          <span class="text-red-600">{{ __('There are unsaved changes') }}</span>
        </ActionMessage>
        <ActionMessage
          :on="form.recentlySuccessful"
          class="mr-3"
        >
          {{ __('Saved') }}
        </ActionMessage>
        <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="save"
        >
          {{ __('Update Recapture Data') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
