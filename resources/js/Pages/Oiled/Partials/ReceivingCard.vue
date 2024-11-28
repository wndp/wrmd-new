<script setup>
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
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
  }
});

const emit = defineEmits(['submitted']);

const doSubmit = () => emit('submitted');
</script>

<template>
  <Panel>
    <template #title>
      {{ __('Primary Care Receiving') }}
    </template>
    <template #content>
      <FormRow
        id="received_at_primary_care_at"
        :label="__('Date Received')"
        :required="true"
        class="col-span-6 md:col-span-2"
      >
        <DatePicker
          id="received_at_primary_care_at"
          v-model="form.received_at_primary_care_at"
          time
        />
        <InputError
          :message="form.errors.received_at_primary_care_at"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="received_at_primary_care_by"
        :label="__('Received By')"
        :required="true"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.received_at_primary_care_by"
          name="received_at_primary_care_by"
        />
        <InputError
          :message="form.errors.received_at_primary_care_by"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="species_confirmed_by"
        :label="__('Species Confirmed By')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.species_confirmed_by"
          name="species_confirmed_by"
        />
        <InputError
          :message="form.errors.species_confirmed_by"
          class="mt-2"
        />
      </FormRow>
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
          {{ __('Update Receiving Data') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
