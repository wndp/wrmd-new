<script setup>
import {computed} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import {formatISO9075} from 'date-fns';
import DialogModal from '@/Components/DialogModal.vue';
import Alert from '@/Components/Alert.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import Autocomplete from '@/Components/FormElements/Autocomplete.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputWithUnit from '@/Components/FormElements/InputWithUnit.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import NutritionPlanIngredients from './NutritionPlanIngredients.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  patientId: {
      type: String,
      required: true
  },
  nutrition: {
    type: Object,
    default: () => { return {} }
  },
  title: {
      type: String,
      required: true
  },
  show: Boolean
});

const emit = defineEmits(['close']);

const form = useForm({
  name: props.nutrition.id ? props.nutrition.name : '',
  started_at: props.nutrition.id ? props.nutrition.started_at : formatISO9075(new Date()),
  ended_at: props.nutrition.id ? props.nutrition.ended_at : null,
  frequency: props.nutrition.id ? props.nutrition.frequency : '',
  frequency_unit_id: props.nutrition.id ? props.nutrition.frequency_unit_id : usePage().props.dailyTaskOptionUiBehaviorIds.nutritionFrequencyHoursId,
  route_id: props.nutrition.id ? props.nutrition.route_id : '',
  description: props.nutrition.id ? props.nutrition.description : '',
  ingredients: props.nutrition.id ? props.nutrition.ingredients ?? [] : [],
});

const cookbookSearchUrl = computed(() => route('cookbook.search', props.patientId));

const setFormulaValues = (obj) => {
  if (obj.defaults) {
    form.frequency = obj.defaults.frequency;
    form.frequency_unit_id = obj.defaults.frequency_unit_id;
    form.route_id = obj.defaults.route_id;
    form.description = obj.defaults.description;
    form.ingredients = obj.defaults.ingredients;
    form.started_at = obj.calculated.started_at;
    form.ended_at = obj.calculated.ended_at;
  }
};

const close = () => emit('close');

const save = () => {
    if (props.nutrition.id) {
        update();
        return;
    }
    store();
};

const store = () => {
    form.post(route('patients.nutrition.store', {
        patient: props.patientId
    }), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            close();
        }
    });
};

const update = () => {
    form.put(route('patients.nutrition.update', {
        patient: props.patientId,
        nutrition: props.nutrition
    }), {
        preserveScroll: true,
        onSuccess: () => close()
    });
};
</script>

<template>
  <DialogModal
    :show="show"
    @close="close"
  >
    <template #title>
      {{ title }}
    </template>
    <template #content>
      <Alert
        v-if="nutrition.id"
        color="red"
        class="mb-4"
      >
        {{ __("Warning: Altering a nutrition plan's dates or description may delete any marked-off tasks.") }}
      </Alert>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-6">
        <FormRow
          id="name"
          class="col-span-6"
          :label="__('Recipe Name')"
        >
          <Autocomplete
            v-model="form.name"
            name="name"
            :placeholder="__('Search cookbook')"
            :source="cookbookSearchUrl"
            :optionFormat="function(obj) {return obj.name}"
            :valueFormat="function(obj) {return obj ? obj.name : obj}"
            @selected="setFormulaValues"
          />
          <InputError
            :message="form.errors.name"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="started_at"
          class="col-span-6 sm:col-span-3"
          :label="__('Start Date')"
        >
          <DatePicker
            id="started_at"
            v-model="form.started_at"
          />
          <InputError
            :message="form.errors.started_at"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="ended_at"
          class="col-span-6 sm:col-span-3"
          :label="__('End Date')"
        >
          <DatePicker
            id="ended_at"
            v-model="form.ended_at"
          />
          <InputError
            :message="form.errors.ended_at"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="frequency"
          class="col-span-6 sm:col-span-3"
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
          class="col-span-6"
          :label="__('Description')"
        >
          <TextareaInput
            v-model="form.description"
            name="description"
          />
          <InputError
            :message="form.errors.description"
            class="mt-2"
          />
        </FormRow>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        {{ __('Nevermind') }}
      </SecondaryButton>
      <PrimaryButton
        class="ml-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="save"
      >
        {{ __('Save Nutrition Plan') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
