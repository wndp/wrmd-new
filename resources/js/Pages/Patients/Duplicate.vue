<script setup>
import {ref, computed} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import PatientLayout from '@/Layouts/PatientLayout.vue';
import { getYear, formatRFC3339 } from 'date-fns';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import Alert from '@/Components/Alert.vue';
import AlertAction from '@/Components/AlertAction.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import IntakeCard from '@/Components/FormCards/IntakeCard.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ValidationErrors from '@/Components/FormElements/ValidationErrors.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import range from 'lodash/range';
import {__} from '@/Composables/Translate';

const props = defineProps({
  patient: {
    type: Object,
    required: true
  }
});

const duplicatesToMake = ref(range(1, 21));

const form = useForm({
  case_year: getYear(new Date()),
  admitted_at: formatRFC3339(new Date(), {representation: 'complete'}),
  reference_number: '',
  microchip_number: '',
  duplicates_to_make: 1,
  admitted_by: usePage().props.auth.user.name,
  transported_by: '',
  found_at: '',
  address_found: props.patient.address_found,
  city_found: props.patient.city_found,
  county_found: props.patient.county_found,
  subdivision_found: props.patient.subdivision_found,
  postal_code_found: props.patient.postal_code_found,
  lat_found: '',
  lng_found: '',
  reason_for_admission: props.patient.reason_for_admission,
  care_by_rescuer: '',
  notes_about_rescue: __('Information copied from patient :caseNumber', {
    caseNumber: usePage().props.admission.case_number
  }),
});

const currentYear = computed(() => new Date().getFullYear());
const notCurrentYear = computed(() => parseInt(form.case_year) !== currentYear.value);

const store = () => {
  form.post(route('patients.duplicate.store', {
    patient: props.patient
  }));
};
</script>

<template>
  <PatientLayout title="Duplicate Patient">
    <h1 class="text-2xl font-semibold text-gray-800">
      {{ __('Duplicate This Patient') }}
    </h1>
    <Alert class="mt-2">
      {{ __("Create a new patient as a copy of this patient. Only this patient's admission and intake data will be copied to the new patient.") }}
    </Alert>
    <Alert
      v-if="notCurrentYear"
      class="mt-2"
      color="red"
    >
      {{ __('You are coping a patient into a previous year (:year)', {year: form.case_year}) }}. <strong class="font-bold">{{ __('Are you sure you want to do that?') }}</strong>
      <AlertAction
        color="red"
        @click="form.case_year = currentYear; getNextCaseNumber()"
      >
        {{ __('No, I want the current year!') }}
      </AlertAction>
    </Alert>
    <ValidationErrors class="mt-4" />
    <form @submit.prevent="store">
      <Panel class="mt-8">
        <template #content>
          <FormRow
            id="case_year"
            :label="__('Case Year')"
            class="col-span-2"
          >
            <SelectInput
              v-model="form.case_year"
              name="case_year"
              :options="$page.props.options.availableYears"
              required
            />
            <InputError
              id="case_year_error"
              class="mt-2"
              :message="form.errors?.case_year"
            />
          </FormRow>
          <FormRow
            id="admitted_at"
            :label="__('Date Admitted')"
            class="col-span-2"
          >
            <DatePicker
              id="admitted_at"
              v-model="form.admitted_at"
              time
            />
            <InputError
              id="admitted_at"
              class="mt-2"
              :message="form.errors?.admitted_at"
            />
          </FormRow>
          <FormRow
            id="reference_number"
            :label="__('Reference #')"
            class="col-span-2 col-start-1"
          >
            <TextInput
              v-model="form.reference_number"
              name="reference_number"
            />
            <InputError
              id="reference_number"
              class="mt-2"
              :message="form.errors?.reference_number"
            />
          </FormRow>
          <FormRow
            id="microchip_number"
            :label="__('Microchip #')"
            class="col-span-2"
          >
            <TextInput
              v-model="form.microchip_number"
              name="microchip_number"
            />
            <InputError
              id="microchip_number"
              class="mt-2"
              :message="form.errors?.microchip_number"
            />
          </FormRow>
          <FormRow
            id="duplicates_to_make"
            :label="__('Number of Duplicates')"
            class="col-span-2"
          >
            <SelectInput
              v-model="form.duplicates_to_make"
              name="duplicates_to_make"
              :options="duplicatesToMake"
            />
            <InputError
              id="duplicates_to_make"
              class="mt-2"
              :message="form.errors?.duplicates_to_make"
            />
          </FormRow>
        </template>
      </Panel>
      <IntakeCard
        :form="form"
        :canSubmit="false"
        class="mt-4"
      />
      <div class="flex items-center justify-end text-right mt-4">
        <ActionMessage
          :on="form.recentlySuccessful"
          class="mr-3"
        >
          {{ __('Saved.') }}
        </ActionMessage>
        <!-- <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="store"
        >
          {{ __('Duplicate Patient') }}
        </PrimaryButton> -->
        <DangerButton
          v-if="notCurrentYear"
          type="submit"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          {{ __('Duplicate Patient into :year', {year: form.case_year}) }}
        </DangerButton>
        <PrimaryButton
          v-else
          type="submit"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          {{ __('Duplicate Patient into :year', {year: form.case_year}) }}
        </PrimaryButton>
      </div>
    </form>
  </PatientLayout>
</template>
