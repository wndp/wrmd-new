<script setup>
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import InputWithUnit from '@/Components/FormElements/InputWithUnit.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import NutritionPlanIngredients from '@/Pages/DailyTasks/Partials/NutritionPlanIngredients.vue';
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
        :label="__('Recipe Name')"
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
        id="start_on_plan_date"
        class="col-span-6 md:col-start-1"
      >
        <Toggle
          v-model="form.start_on_plan_date"
          dusk="start_on_plan_date"
        >
          {{ __('Start nutrition plan on the same day it is written?') }}
        </Toggle>
      </FormRow>
      <FormRow
        v-if="form.start_on_plan_date"
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
        id="frequency"
        class="col-span-6 sm:col-span-3 md:col-start-1"
        :label="__('Frequency')"
      >
        <div class="flex items-center">
          <span class="mr-2 text-base text-gray-700">Every</span>
          <InputWithUnit
            v-model:text="form.frequency"
            v-model:unit="form.frequency_unit_id"
            name="frequency"
            type="number"
            step="any"
            min="0"
            :units="$page.props.options.dailyTaskNutritionFrequenciesOptions"
          />
        </div>
        <InputError
          :message="form.errors.frequency"
          class="mt-2"
        />
        <InputError
          :message="form.errors.frequency_unit_id"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="route_id"
        class="col-span-6 sm:col-span-3"
        :label="__('Route')"
      >
        <SelectInput
          v-model="form.route_id"
          name="route_id"
          :options="$page.props.options.dailyTaskNutritionRoutesOptions"
          hasBlankOption
        />
        <InputError
          :message="form.errors.route_id"
          class="mt-2"
        />
      </FormRow>
      <p class="col-span-6 text-sm text-gray-500">
        {{ __('Add individual ingredients or write the plan in the description box below.') }}
      </p>
      <FormRow
        id="ingredients"
        class="col-span-6"
      >
        <NutritionPlanIngredients
          :form="form"
        />
      </FormRow>
      <FormRow
        id="description"
        :label="__('Description')"
        class="col-span-6"
      >
        <TextareaInput
          v-model="form.description"
          name="description"
        />
        <InputError
          class="mt-2"
          :message="form.errors?.description"
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
