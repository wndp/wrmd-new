<template>
  <AppLayout title="Admissions">
    <template #header>
      <div class="flex justify-between">
        <h1 class="text-2xl font-semibold text-gray-900 mb-2">
          {{ __('Quick Admit New Patient') }}
        </h1>
      </div>
      <h2
        class="text-xl font-semibold mb-8"
        :class="[notCurrentYear ? 'text-red-700' : 'text-gray-800']"
      >
        {{ __('Next Case Number') }}: {{ nextCaseNumber }}
      </h2>
    </template>
    <Alert
      v-if="notCurrentYear"
      class="mt-2"
      color="red"
    >
      {{ __('You are admitting a patient into a previous year (:year)', {year: form.identifiers.case_year}) }}. <strong class="font-bold">{{ __('Are you sure you want to do that?') }}</strong>
      <AlertAction
        color="red"
        @click="form.identifiers.case_year = currentYear; getNextCaseNumber(currentYear)"
      >
        {{ __('No, I want the current year!') }}
      </AlertAction>
    </Alert>
    <ValidationErrors class="mt-4" />
    <form @submit.prevent="store">
      <CreatePatientIdentifiers
        v-model="form.identifiers"
        class="mt-8"
        @case-year-change="getNextCaseNumber"
      />
      <Panel class="mt-8">
        <template #heading>
          {{ __('Intake') }}
        </template>
        <div class="space-y-4 sm:space-y-2">
          <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
            <Label
              for="admitted_by"
              class="sm:text-right"
            >
              {{ __('Admitted By') }}
              <RequiredInput />
            </Label>
            <div class="col-span-2 mt-1 sm:mt-0">
              <Input
                v-model="form.admitted_by"
                name="admitted_by"
                required
              />
              <InputError
                class="mt-2"
                :message="form.errors.admitted_by"
              />
            </div>
            <Label
              for="transported_by"
              class="sm:text-right mt-4 sm:mt-0"
            >{{ __('Transported By') }}</Label>
            <div class="col-span-2 mt-1 sm:mt-0">
              <Input
                v-model="form.transported_by"
                name="transported_by"
              />
            </div>
          </div>
          <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
            <Label
              for="address_found"
              class="sm:text-right"
            >
              {{ __('Address Found') }}
              <RequiredInput />
            </Label>
            <div class="col-span-5 mt-1 sm:mt-0">
              <Input
                v-model="form.address_found"
                name="address_found"
                required
              />
              <InputError
                class="mt-2"
                :message="form.errors.address_found"
              />
            </div>
          </div>
          <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
            <Label
              for="city_found"
              class="sm:text-right"
            >
              {{ __('City / State Found') }}
              <RequiredInput />
            </Label>
            <div class="col-span-2 mt-1 sm:mt-0 flex">
              <Input
                v-model="form.city_found"
                name="city_found"
                class="mr-2"
                required
              />
              <Select
                v-model="form.subdivision_found"
                name="subdivision_found"
                :options="$page.props.options.subdivisions"
                required
              />
              <InputError
                class="mt-2"
                :message="form.errors.city_found"
              />
              <InputError
                class="mt-2"
                :message="form.errors.subdivision_found"
              />
            </div>
            <Label
              for="found_at"
              class="sm:text-right mt-4 sm:mt-0"
            >
              {{ __('Date Found') }}
              <RequiredInput />
            </Label>
            <div class="col-span-2 mt-1 sm:mt-0">
              <DatePicker
                id="found_at"
                v-model="form.found_at"
                required
              />
              <InputError
                class="mt-2"
                :message="form.errors.found_at"
              />
            </div>
          </div>
          <div
            v-if="settings.showGeolocationFields"
            class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center"
          >
            <Label
              for="lat_found"
              class="sm:text-right"
            >
              {{ __('Latitude Found') }}
            </Label>
            <div class="col-span-2 mt-1 sm:mt-0">
              <Input
                v-model="form.lat_found"
                name="lat_found"
                type="number"
                step="any"
                min="-90"
                max="90"
              />
              <InputError
                class="mt-2"
                :message="form.errors.lat_found"
              />
            </div>
            <Label
              for="lng_found"
              class="sm:text-right mt-4 sm:mt-0"
            >
              {{ __('Longitude Found') }}
            </Label>
            <div class="col-span-2 mt-1 sm:mt-0">
              <Input
                v-model="form.lng_found"
                name="lng_found"
                type="number"
                step="any"
                min="-180"
                max="180"
              />
              <InputError
                class="mt-2"
                :message="form.errors.lng_found"
              />
            </div>
          </div>
          <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
            <Label
              for="reasons_for_admission"
              class="sm:text-right"
            >
              {{ __('Reasons For Admission') }}
              <RequiredInput />
            </Label>
            <div class="col-span-5 mt-1 sm:mt-0">
              <Input
                v-model="form.reasons_for_admission"
                name="reasons_for_admission"
                required
              />
              <InputError
                class="mt-2"
                :message="form.errors.reasons_for_admission"
              />
            </div>
          </div>
          <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
            <Label
              for="care_by_rescuer"
              class="sm:text-right"
            >{{ __('Care by Rescuer') }}</Label>
            <div class="col-span-5 mt-1 sm:mt-0">
              <Input
                v-model="form.care_by_rescuer"
                name="care_by_rescuer"
              />
            </div>
          </div>
          <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
            <Label
              for="notes_about_rescue"
              class="sm:text-right"
            >{{ __('Notes About Rescue') }}</Label>
            <div class="col-span-5 mt-1 sm:mt-0">
              <Input
                v-model="form.notes_about_rescue"
                name="notes_about_rescue"
              />
            </div>
          </div>
          <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
            <Label
              for="weight"
              class="md:text-right"
            >{{ __('Weight') }}</Label>
            <div class="col-span-2 mt-1 sm:mt-0">
              <InputWithUnit
                v-model:text="form.weight"
                v-model:unit="form.weight_unit"
                name="weight"
                type="number"
                step="any"
                min="0"
                :units="weightUnits"
              />
            </div>
            <Label
              for="age"
              class="md:text-right mt-4 sm:mt-0"
            >{{ __('Age') }}</Label>
            <div class="col-span-2 mt-1 sm:mt-0">
              <InputWithUnit
                v-model:text="form.age"
                v-model:unit="form.age_unit"
                name="age"
                :units="ageUnits"
              />
            </div>
          </div>
          <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
            <Label
              for="attitude"
              class="md:text-right"
            >{{ __('Attitude') }}</Label>
            <div class="col-span-2 mt-1 sm:mt-0">
              <Select
                v-model="form.attitude"
                name="attitude"
                :options="attitudes"
              />
            </div>
            <Label
              for="sex"
              class="md:text-right mt-4 sm:mt-0"
            >{{ __('Sex') }}</Label>
            <div class="col-span-2 mt-1 sm:mt-0">
              <Select
                v-model="form.sex"
                name="sex"
                :options="sexes"
              />
            </div>
          </div>
        </div>
      </Panel>
      <OutcomeForm
        v-bind="$props"
        v-model="form.outcome"
        :can-submit="false"
        class="mt-8"
      />
      <div class="flex items-center justify-start text-left mt-8">
        <DangerButton
          v-if="notCurrentYear"
          type="submit"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          {{ __('Admit Patient into :year', {year: form.identifiers.case_year}) }}
        </DangerButton>
        <PrimaryButton
          v-else
          type="submit"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          {{ __('Admit Patient into :year', {year: form.identifiers.case_year}) }}
        </PrimaryButton>
        <ActionMessage
          :on="form.recentlySuccessful"
          class="ml-3"
        >
          {{ __('Saved.') }}
        </ActionMessage>
      </div>
    </form>
  </AppLayout>
</template>

<script setup>
import { inject, ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue';
import CreatePatientIdentifiers from './Partials/CreatePatientIdentifiers.vue';
import Panel from '@/Components/Panel.vue';
import Alert from '@/Components/Alert.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputWithUnit from '@/Components/FormElements/InputWithUnit.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import AlertAction from '@/Components/AlertAction.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import ValidationErrors from '@/Components/FormElements/ValidationErrors.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import RequiredInput from '@/Components/FormElements/RequiredInput.vue';
import OutcomeForm from '@/Components/Forms/OutcomeForm.vue';
import { formatISO9075 } from 'date-fns';

let route = inject('route');

let formatCaseNumber = (year, id) => {
  year = year.toString().substring(year.toString().length-2);
  return `${year}-${id}`;
};

let nextCaseNumber = ref(formatCaseNumber(usePage().props.lastCaseId.year, usePage().props.lastCaseId.id+1));

let getNextCaseNumber = (caseYear) => {
  window.axios.get(route('admissions.year',{year: caseYear})).then(response => {
    nextCaseNumber.value = formatCaseNumber(
      response.data.year,
      response.data.last_case_id + 1
    );
  });
};

let store = () => {
  form.transform(data => {
    return _.chain(data)
      .merge(data.identifiers, data.outcome)
      .omit(['identifiers', 'outcome']);
  }).post(route('patients.quick_admit.store'), {
    onSuccess: () => {
      let year = form.identifiers.case_year;
      form.reset();
      form.identifiers.case_year = year;
      getNextCaseNumber(year);
    }
  });
}

let form = useForm({
  identifiers: {
    case_year: usePage().props.lastCaseId.year,
    admitted_at: formatISO9075(new Date()),
    common_name: '',
    morph: '',
    cases_to_create: 1,
    reference_number: '',
    microchip_number: '',
  },
  transported_by: '',
  admitted_by: '',
  found_at: '',
  address_found: '',
  city_found: '',
  subdivision_found: '',
  reasons_for_admission: '',
  care_by_rescuer: '',
  notes_about_rescue: '',
  weight: '',
  weight_unit: '',
  age: '',
  age_unit: '',
  sex: '',
  attitude: '',
  outcome: {
    disposition: 'Pending',
    dispositioned_at: '',
    release_type: '',
    transfer_type: '',
    disposition_address: '',
    disposition_city: '',
    disposition_subdivision: '',
    disposition_postal_code: '',
    disposition_lat: '',
    disposition_lng: '',
    reason_for_disposition: '',
    dispositioned_by: '',
    carcass_saved: false
  }
});

let currentYear = computed(() => new Date().getFullYear());
let notCurrentYear = computed(() => parseInt(form.identifiers.case_year) !== currentYear.value);
let settings = computed(() => usePage().props.settings);
let weightUnits = computed(() => usePage().props.options.weightUnits);
let ageUnits = computed(() => usePage().props.options.ageUnits);
let attitudes = computed(() => usePage().props.options.attitudes);
let sexes = computed(() => usePage().props.options.sexes);
</script>
