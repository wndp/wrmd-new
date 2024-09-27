<script setup>
import {computed} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import { formatISO9075 } from 'date-fns';
import DialogModal from '@/Components/DialogModal.vue';
import Alert from '@/Components/Alert.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import Autocomplete from '@/Components/FormElements/Autocomplete.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import Toggle from '@/Components/FormElements/Toggle.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import InputWithUnit from '@/Components/FormElements/InputWithUnit.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  patientId: {
      type: String,
      required: true
  },
  prescription: {
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
  drug: props.prescription.id ? props.prescription.drug : null,
  concentration: props.prescription.id ? props.prescription.concentration : null,
  concentration_unit_id: props.prescription.id ? props.prescription.concentration_unit_id : usePage().props.dailyTaskOptionUiBehaviorIds.mgPerMlId,
  dosage: props.prescription.id ? props.prescription.dosage : null,
  dosage_unit_id: props.prescription.id ? props.prescription.dosage_unit_id : usePage().props.dailyTaskOptionUiBehaviorIds.mgPerKgId,
  loading_dose: props.prescription.id ? props.prescription.loading_dose : null,
  loading_dose_unit_id: props.prescription.id ? props.prescription.loading_dose_unit_id : usePage().props.dailyTaskOptionUiBehaviorIds.mlId,
  dose: props.prescription.id ? props.prescription.dose : null,
  dose_unit_id: props.prescription.id ? props.prescription.dose_unit_id : usePage().props.dailyTaskOptionUiBehaviorIds.mlId,
  frequency_id: props.prescription.id ? props.prescription.frequency_id : null,
  route_id: props.prescription.id ? props.prescription.route_id : null,
  rx_started_at: props.prescription.id ? props.prescription.rx_started_at : formatISO9075(new Date()),
  rx_ended_at: props.prescription.id ? props.prescription.rx_ended_at : null,
  is_controlled_substance: props.prescription.id ? props.prescription.is_controlled_substance : false,
  instructions: props.prescription.id ? props.prescription.instructions : null,
});

const formularySearchUrl = computed(() => route('formulary.search', props.patientId));

const close = () => emit('close');

const setFormulaValues = (obj) => {
  if (obj.defaults) {
    form.concentration = obj.defaults.concentration
    form.concentration_unit_id = obj.defaults.concentration_unit_id
    form.route_id = obj.defaults.route_id
    form.dosage = obj.defaults.dosage
    form.dosage_unit_id = obj.defaults.dosage_unit_id
    form.frequency_id = obj.defaults.frequency_id
    form.dose = obj.calculated.dose
    form.dose_unit_id = obj.calculated.dose_unit_id
    form.rx_started_at = obj.calculated.rx_started_at,
    form.rx_ended_at = obj.calculated.rx_ended_at,
    form.loading_dose = obj.defaults.loading_dose
    form.loading_dose_unit_id = obj.defaults.loading_dose_unit_id
    form.is_controlled_substance = obj.defaults.is_controlled_substance
    form.instructions = obj.defaults.instructions
  }
};

const save = () => {
    if (props.prescription.id) {
        update();
        return;
    }
    store();
};

const store = () => {
    form.post(route('patients.prescription.store', {
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
    form.put(route('patients.prescription.update', {
        patient: props.patientId,
        prescription: props.prescription
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
        v-if="prescription.id"
        color="red"
        class="mb-4"
      >
        {{ __("Warning: Altering a prescription's dates or description may delete any marked-off tasks.") }}
      </Alert>
      <div class="space-y-4 sm:space-y-2">
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="drug"
            class="sm:text-right"
          >
            {{ __('Medication') }}
          </InputLabel>
          <div class="col-span-5 mt-1 sm:mt-0">
            <Autocomplete
              v-model="form.drug"
              name="drug"
              :placeholder="__('Search formulary')"
              :source="formularySearchUrl"
              :optionFormat="function(obj) {return obj.name}"
              :valueFormat="function(obj) {return obj.defaults ? obj.defaults.drug : obj}"
              @selected="setFormulaValues"
            />
            <InputError
              :message="form.errors.drug"
              class="mt-1"
            />
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-2 sm:gap-x-2">
          <div class="space-y-4 sm:space-y-2">
            <div class="sm:grid sm:grid-cols-3 sm:gap-x-2 sm:items-center">
              <InputLabel
                for="concentration"
                class="sm:text-right"
              >
                {{ __('Concentration') }}
              </InputLabel>
              <div class="col-span-2 mt-1 sm:mt-0">
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
                  :message="form.errors.concentration_unit_id"
                  class="mt-1"
                />
              </div>
            </div>
            <div class="sm:grid sm:grid-cols-3 sm:gap-x-2 sm:items-center">
              <InputLabel
                for="dosage"
                class="sm:text-right"
              >
                {{ __('Dosage') }}
              </InputLabel>
              <div class="col-span-2 mt-1 sm:mt-0">
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
                  :message="form.errors.dosage_unit_id"
                  class="mt-1"
                />
              </div>
            </div>
            <div class="sm:grid sm:grid-cols-3 sm:gap-x-2 sm:items-center">
              <InputLabel
                for="dose"
                class="sm:text-right"
              >
                {{ __('Dose') }}
              </InputLabel>
              <div class="col-span-2 mt-1 sm:mt-0">
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
                  :message="form.errors.dose_unit_id"
                  class="mt-1"
                />
              </div>
            </div>
          </div>
          <div class="space-y-4 sm:space-y-2 mt-4 sm:mt-0">
            <div class="sm:grid sm:grid-cols-3 sm:gap-x-2 sm:items-center">
              <InputLabel
                for="route"
                class="sm:text-right"
              >
                {{ __('Route') }}
              </InputLabel>
              <div class="col-span-2 mt-1 sm:mt-0">
                <SelectInput
                  v-model="form.route_id"
                  name="route"
                  :options="$page.props.options.dailyTaskRoutesOptions"
                />
                <InputError
                  :message="form.errors.route_id"
                  class="mt-1"
                />
              </div>
            </div>
            <div class="sm:grid sm:grid-cols-3 sm:gap-x-2 sm:items-center">
              <InputLabel
                for="frequency"
                class="sm:text-right"
              >
                {{ __('Frequency') }}
              </InputLabel>
              <div class="col-span-2 mt-1 sm:mt-0">
                <SelectInput
                  v-model="form.frequency_id"
                  name="frequency"
                  :options="$page.props.options.dailyTaskFrequenciesOptions"
                />
                <InputError
                  :message="form.errors.frequency_id"
                  class="mt-1"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="rx_started_at"
            class="sm:text-right"
          >
            {{ __('Start Date') }}
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <DatePicker
              id="rx_started_at"
              v-model="form.rx_started_at"
            />
            <InputError
              :message="form.errors.rx_started_at"
              class="mt-1"
            />
          </div>
          <InputLabel
            for="rx_ended_at"
            class="sm:text-right mt-4 sm:mt-0"
          >
            {{ __('End Date') }}
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <DatePicker
              id="rx_ended_at"
              v-model="form.rx_ended_at"
            />
            <InputError
              :message="form.errors.rx_ended_at"
              class="mt-1"
            />
          </div>
        </div>
        <div class="pt-2">
          <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center border-t border-gray-300 border-dashed pt-4">
            <InputLabel
              for="loading_dose"
              class="sm:text-right"
            >
              {{ __('Loading Dose') }}
            </InputLabel>
            <div class="col-span-2 mt-1 sm:mt-0">
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
                :message="form.errors.loading_dose_unit_id"
                class="mt-1"
              />
            </div>
            <div class="col-start-5 col-span-2 mt-4 sm:mt-0">
              <Toggle
                v-model="form.is_controlled_substance"
                dusk="is_controlled_substance"
              >
                {{ __('Is a Controlled Substance?') }}
              </Toggle>
            </div>
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="instructions"
            class="sm:text-right"
          >
            {{ __('Instructions') }}
          </InputLabel>
          <div class="col-span-5 mt-1 sm:mt-0">
            <TextInput
              v-model="form.instructions"
              name="instructions"
            />
          </div>
        </div>
        <div class="pt-2">
          <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center border-t border-gray-300 border-dashed pt-4">
            <InputLabel
              for="veterinarian"
              class="sm:text-right"
            >
              {{ __('Veterinarian') }}
            </InputLabel>
            <div class="col-span-5 mt-1 sm:mt-0">
              <TextInput
                v-model="form.veterinarian"
                name="veterinarian"
              />
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        Nevermind
      </SecondaryButton>
      <PrimaryButton
        class="ml-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="save"
      >
        {{ __('Save Prescription') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
