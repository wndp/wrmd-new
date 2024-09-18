<script setup>
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import InputWithUnit from '@/Components/FormElements/InputWithUnit.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

defineProps({
  title: {
    type: String,
    required: true
  },
  action: {
    type: String,
    required: true
  },
  form: {
    type: Object,
    required: true
  },
});

const emit = defineEmits(['saved']);
</script>

<template>
  <Panel class="mt-4">
    <template #title>
      {{ title }}
    </template>
    <template #content>
      <FormRow
        id="name"
        :label="__('Formula Name')"
        :required="true"
        class="col-span-6"
      >
        <TextInput
          v-model="form.name"
          name="name"
          :required="true"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.name"
        />
      </FormRow>
      <hr class="col-span-6 border-t border-gray-300 border-dashed">
      <FormRow
        id="drug"
        :label="__('Medication')"
        :required="true"
        class="col-span-6"
      >
        <TextInput
          v-model="form.drug"
          name="drug"
          :required="true"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.drug"
        />
      </FormRow>
      <FormRow
        id="concentration"
        :label="__('Concentration')"
        class="col-span-6 md:col-span-3"
      >
        <InputWithUnit
          v-model:text="form.concentration"
          v-model:unit="form.concentration_unit_id"
          name="concentration"
          type="number"
          step="any"
          min="0"
          :units="$page.props.options.dailyTaskConcentrationUnitsOptions"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.concentration"
        />
      </FormRow>
      <FormRow
        id="route"
        :label="__('Route')"
        class="col-span-6 md:col-span-3"
      >
        <SelectInput
          v-model="form.route_id"
          name="route"
          :options="$page.props.options.dailyTaskRoutesOptions"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.route"
        />
      </FormRow>
      <FormRow
        id="dosage"
        :label="__('Dosage')"
        class="col-span-6 md:col-span-3"
      >
        <InputWithUnit
          v-model:text="form.dosage"
          v-model:unit="form.dosage_unit_id"
          name="dosage"
          type="number"
          step="any"
          min="0"
          :units="$page.props.options.dailyTaskDosageUnitsOptions"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.dosage"
        />
      </FormRow>
      <FormRow
        id="frequency"
        :label="__('Frequency')"
        class="col-span-6 md:col-span-3"
      >
        <SelectInput
          v-model="form.frequency_id"
          name="frequency"
          :options="$page.props.options.dailyTaskFrequenciesOptions"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.frequency"
        />
      </FormRow>
      <FormRow
        id="auto_calculate_dose"
        class="col-span-6 md:col-start-1"
      >
        <Toggle
          v-model="form.auto_calculate_dose"
          dusk="auto_calculate_dose"
        >
          {{ __('Auto-calculate dose based on most recent weight?') }}
        </Toggle>
      </FormRow>
      <FormRow
        v-if="! form.auto_calculate_dose"
        id="dose"
        :label="__('Dose')"
        class="col-span-6 md:col-span-3"
      >
        <InputWithUnit
          v-model:text="form.dose"
          v-model:unit="form.dose_unit_id"
          name="dose"
          type="number"
          step="any"
          min="0"
          :units="$page.props.options.dailyTaskDoseUnitsOptions"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.dose"
        />
      </FormRow>
      <FormRow
        id="start_on_prescription_date"
        class="col-span-6 md:col-start-1"
      >
        <Toggle
          v-model="form.start_on_prescription_date"
          dusk="start_on_prescription_date"
        >
          {{ __('Start prescription on the same day it is written?') }}
        </Toggle>
      </FormRow>
      <FormRow
        v-if="form.start_on_prescription_date"
        id="duration"
        :label="__('Duration in Days')"
        class="col-span-6 md:col-span-3"
      >
        <TextInput
          v-model="form.duration"
          name="duration"
          type="number"
          step="any"
          min="0"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.duration"
        />
      </FormRow>
      <FormRow
        id="loading_dose"
        :label="__('Loading Dose')"
        class="col-span-6 md:col-span-3 md:col-start-1"
      >
        <InputWithUnit
          v-model:text="form.loading_dose"
          v-model:unit="form.loading_dose_unit_id"
          name="loading_dose"
          type="number"
          step="any"
          min="0"
          :units="$page.props.options.dailyTaskDoseUnitsOptions"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.loading_dose"
        />
      </FormRow>
      <FormRow
        id="is_controlled_substance"
        class="col-span-6 md:col-span-3"
      >
        <Toggle
          v-model="form.is_controlled_substance"
          dusk="start_on_prescription_date"
          class="md:mt-8"
        >
          {{ __('Is a Controlled Substance?') }}
        </Toggle>
      </FormRow>
      <FormRow
        id="instructions"
        :label="__('Instructions')"
        class="col-span-6"
      >
        <TextInput
          v-model="form.instructions"
          name="instructions"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.instructions"
        />
      </FormRow>
    </template>
    <template #actions>
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
          @click="emit('saved')"
        >
          {{ action }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
