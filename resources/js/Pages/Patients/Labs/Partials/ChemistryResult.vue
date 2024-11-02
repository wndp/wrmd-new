<script setup>
import {useForm} from '@inertiajs/vue3';
import { formatISO9075 } from 'date-fns';
import DialogModal from '@/Components/DialogModal.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import InputWithUnit from '@/Components/FormElements/InputWithUnit.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  patientId: {
    type: String,
    required: true
  },
  labReport: {
    type: Object,
    default: () => { return {} }
  },
  show: Boolean
});

const emit = defineEmits(['close']);

const form = useForm({
  analysis_date_at: props.labReport.id ? props.labReport.analysis_date_at : formatISO9075(new Date()),
  technician: props.labReport.id ? props.labReport.technician : '',
  accession_number: props.labReport.id ? props.labReport.accession_number : '',
  analysis_facility: props.labReport.id ? props.labReport.analysis_facility : '',
  comments: props.labReport.id ? props.labReport.comments : '',

  ast: props.labReport.id ? props.labReport.lab_result.ast : '',
  ck: props.labReport.id ? props.labReport.lab_result.ck : '',
  ggt: props.labReport.id ? props.labReport.lab_result.ggt : '',
  amy: props.labReport.id ? props.labReport.lab_result.amy : '',
  alb: props.labReport.id ? props.labReport.lab_result.alb : '',
  alp: props.labReport.id ? props.labReport.lab_result.alp : '',
  alt: props.labReport.id ? props.labReport.lab_result.alt : '',
  tp: props.labReport.id ? props.labReport.lab_result.tp : '',
  glob: props.labReport.id ? props.labReport.lab_result.glob : '',
  bun: props.labReport.id ? props.labReport.lab_result.bun : '',
  chol: props.labReport.id ? props.labReport.lab_result.chol : '',
  crea: props.labReport.id ? props.labReport.lab_result.crea : '',
  ba: props.labReport.id ? props.labReport.lab_result.ba : '',
  glu: props.labReport.id ? props.labReport.lab_result.glu : '',
  ca: props.labReport.id ? props.labReport.lab_result.ca : '',
  ca_unit_id: props.labReport.id ? props.labReport.lab_result.ca_unit_id : '',
  p: props.labReport.id ? props.labReport.lab_result.p : '',
  p_unit_id: props.labReport.id ? props.labReport.lab_result.p_unit_id : '',
  cl: props.labReport.id ? props.labReport.lab_result.cl : '',
  cl_unit_id: props.labReport.id ? props.labReport.lab_result.cl_unit_id : '',
  k: props.labReport.id ? props.labReport.lab_result.k : '',
  k_unit_id: props.labReport.id ? props.labReport.lab_result.k_unit_id : '',
  na: props.labReport.id ? props.labReport.lab_result.na : '',
  na_unit_id: props.labReport.id ? props.labReport.lab_result.na_unit_id : '',
  total_bilirubin: props.labReport.id ? props.labReport.lab_result.total_bilirubin : '',
  ag_ratio: props.labReport.id ? props.labReport.lab_result.ag_ratio : '',
  tri: props.labReport.id ? props.labReport.lab_result.tri : '',
  nak_ratio: props.labReport.id ? props.labReport.lab_result.nak_ratio : '',
  cap_ratio: props.labReport.id ? props.labReport.lab_result.cap_ratio : '',
  ua: props.labReport.id ? props.labReport.lab_result.ua : '',
});

const close = () => emit('close');

const save = () => {
    if (props.labReport.id) {
        update();
        return;
    }
    store();
};

const store = () => {
    form.post(route('patients.lab-reports.chemistry.store', {
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
    form.put(route('patients.lab-reports.chemistry.update', {
        patient: props.patientId,
        labResult: props.labReport.lab_result.id
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
      {{ __('CBC (Complete Blood Count)') }}
    </template>
    <template #content>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-6">
        <FormRow
          id="analysis_date_at"
          class="sm:col-span-3"
          :label="__('Date')"
        >
          <DatePicker
            v-model="form.analysis_date_at"
            name="analysis_date_at"
          />
          <InputError
            :message="form.errors.analysis_date_at"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="technician"
          class="sm:col-span-3"
          :label="__('Technician')"
        >
          <TextInput
            v-model="form.technician"
            name="technician"
          />
          <InputError
            :message="form.errors.technician"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="accession_number"
          class="sm:col-span-3"
          :label="__('Accession Number')"
        >
          <TextInput
            v-model="form.accession_number"
            name="accession_number"
          />
          <InputError
            :message="form.errors.accession_number"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="analysis_facility"
          class="sm:col-span-3"
          :label="__('Analysis Facility')"
        >
          <TextInput
            v-model="form.analysis_facility"
            name="analysis_facility"
          />
          <InputError
            :message="form.errors.analysis_facility"
            class="mt-2"
          />
        </FormRow>
        <hr class="sm:col-span-6">
        <FormRow
          id="ast"
          class="sm:col-span-2"
          :label="__('AST (U/L)')"
        >
          <TextInput
            v-model="form.ast"
            name="ast"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.ast"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="ck"
          class="sm:col-span-2"
          :label="__('AST (U/L)')"
        >
          <TextInput
            v-model="form.ck"
            name="ck"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.ck"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="ggt"
          class="sm:col-span-2"
          :label="__('GGT (U/L)')"
        >
          <TextInput
            v-model="form.ggt"
            name="ggt"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.ggt"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="amy"
          class="sm:col-span-2"
          :label="__('Amylase (U/L)')"
        >
          <TextInput
            v-model="form.amy"
            name="amy"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.amy"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="alb"
          class="sm:col-span-2"
          :label="__('Albumin (g/dL)')"
        >
          <TextInput
            v-model="form.alb"
            name="alb"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.alb"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="alp"
          class="sm:col-span-2"
          :label="__('ALP (g/dL)')"
        >
          <TextInput
            v-model="form.alp"
            name="alp"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.alp"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="alt"
          class="sm:col-span-2"
          :label="__('ALT (g/dL)')"
        >
          <TextInput
            v-model="form.alt"
            name="alt"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.alt"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="tp"
          class="sm:col-span-2"
          :label="__('TP (g/dL)')"
        >
          <TextInput
            v-model="form.tp"
            name="tp"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.tp"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="glob"
          class="sm:col-span-2"
          :label="__('Globulin (g/dL)')"
        >
          <TextInput
            v-model="form.glob"
            name="glob"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.glob"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="bun"
          class="sm:col-span-2"
          :label="__('BUN (mg/dL)')"
        >
          <TextInput
            v-model="form.bun"
            name="bun"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.bun"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="chol"
          class="sm:col-span-2"
          :label="__('Cholesterol (mg/dL)')"
        >
          <TextInput
            v-model="form.chol"
            name="chol"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.chol"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="crea"
          class="sm:col-span-2"
          :label="__('Creatinine (mg/dL)')"
        >
          <TextInput
            v-model="form.crea"
            name="crea"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.crea"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="ba"
          class="sm:col-span-2"
          :label="__('Bile Acids (umol/L)')"
        >
          <TextInput
            v-model="form.ba"
            name="ba"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.ba"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="ca"
          class="sm:col-span-2"
          :label="__('Calcium')"
        >
          <InputWithUnit
            v-model:text="form.ca"
            v-model:unit="form.ca_unit_id"
            name="ca"
            type="number"
            step="any"
            min="0"
            :units="$page.props.options.labChemistryUnitsOptions"
          />
          <InputError
            :message="form.errors.ca"
            class="mt-2"
          />
          <InputError
            :message="form.errors.ca_unit_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="p"
          class="sm:col-span-2"
          :label="__('Phosphorus')"
        >
          <InputWithUnit
            v-model:text="form.p"
            v-model:unit="form.p_unit_id"
            name="p"
            type="number"
            step="any"
            min="0"
            :units="$page.props.options.labChemistryUnitsOptions"
          />
          <InputError
            :message="form.errors.p"
            class="mt-2"
          />
          <InputError
            :message="form.errors.p_unit_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="cl"
          class="sm:col-span-2"
          :label="__('Chloride')"
        >
          <InputWithUnit
            v-model:text="form.cl"
            v-model:unit="form.cl_unit_id"
            name="cl"
            type="number"
            step="any"
            min="0"
            :units="$page.props.options.labChemistryUnitsOptions"
          />
          <InputError
            :message="form.errors.cl"
            class="mt-2"
          />
          <InputError
            :message="form.errors.cl_unit_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="k"
          class="sm:col-span-2"
          :label="__('Potassium')"
        >
          <InputWithUnit
            v-model:text="form.k"
            v-model:unit="form.k_unit_id"
            name="k"
            type="number"
            step="any"
            min="0"
            :units="$page.props.options.labChemistryUnitsOptions"
          />
          <InputError
            :message="form.errors.k"
            class="mt-2"
          />
          <InputError
            :message="form.errors.k_unit_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="na"
          class="sm:col-span-2"
          :label="__('Sodium')"
        >
          <InputWithUnit
            v-model:text="form.na"
            v-model:unit="form.na_unit_id"
            name="na"
            type="number"
            step="any"
            min="0"
            :units="$page.props.options.labChemistryUnitsOptions"
          />
          <InputError
            :message="form.errors.na"
            class="mt-2"
          />
          <InputError
            :message="form.errors.na_unit_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="total_bilirubin"
          class="sm:col-span-2"
          :label="__('Total Bilirubin')"
        >
          <TextInput
            v-model="form.total_bilirubin"
            name="total_bilirubin"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.total_bilirubin"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="ag_ratio"
          class="sm:col-span-2"
          :label="__('A / G Ratio')"
        >
          <TextInput
            v-model="form.ag_ratio"
            name="ag_ratio"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.ag_ratio"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="tri"
          class="sm:col-span-2"
          :label="__('Triglyceride (mg/dL)')"
        >
          <TextInput
            v-model="form.tri"
            name="tri"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.tri"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="nak_ratio"
          class="sm:col-span-2"
          :label="__('Na / K Ratio')"
        >
          <TextInput
            v-model="form.nak_ratio"
            name="nak_ratio"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.nak_ratio"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="cap_ratio"
          class="sm:col-span-2"
          :label="__('Ca / P Ratio')"
        >
          <TextInput
            v-model="form.cap_ratio"
            name="cap_ratio"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.cap_ratio"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="glu"
          class="sm:col-span-2"
          :label="__('Glucose')"
        >
          <TextInput
            v-model="form.glu"
            name="glu"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.glu"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="ua"
          class="sm:col-span-2"
          :label="__('Uric Acid')"
        >
          <TextInput
            v-model="form.ua"
            name="ua"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.ua"
            class="mt-2"
          />
        </FormRow>

        <hr class="sm:col-span-6">
        <FormRow
          id="comments"
          class="sm:col-span-6"
          :label="__('Comments')"
        >
          <TextareaInput
            v-model="form.comments"
            name="comments"
          />
          <InputError
            :message="form.errors.comments"
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
        {{ __('Save Lab Result') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
