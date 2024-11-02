<script setup>
import {useForm} from '@inertiajs/vue3';
import { formatISO9075 } from 'date-fns';
import DialogModal from '@/Components/DialogModal.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
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

  collection_method_id: props.labReport.id ? props.labReport.lab_result.collection_method_id : '',
  sg: props.labReport.id ? props.labReport.lab_result.sg : '',
  ph: props.labReport.id ? props.labReport.lab_result.ph : '',
  pro: props.labReport.id ? props.labReport.lab_result.pro : '',
  glu: props.labReport.id ? props.labReport.lab_result.glu : '',
  ket: props.labReport.id ? props.labReport.lab_result.ket : '',
  bili: props.labReport.id ? props.labReport.lab_result.bili : '',
  ubg: props.labReport.id ? props.labReport.lab_result.ubg : '',
  nitrite: props.labReport.id ? props.labReport.lab_result.nitrite : '',
  bun: props.labReport.id ? props.labReport.lab_result.bun : '',
  leuc: props.labReport.id ? props.labReport.lab_result.leuc : '',
  blood: props.labReport.id ? props.labReport.lab_result.blood : '',
  color: props.labReport.id ? props.labReport.lab_result.color : '',
  turbidity_id: props.labReport.id ? props.labReport.lab_result.turbidity_id : '',
  odor_id: props.labReport.id ? props.labReport.lab_result.odor_id : '',
  crystals: props.labReport.id ? props.labReport.lab_result.crystals : '',
  casts: props.labReport.id ? props.labReport.lab_result.casts : '',
  cells: props.labReport.id ? props.labReport.lab_result.cells : '',
  microorganisms: props.labReport.id ? props.labReport.lab_result.microorganisms : '',
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
    form.post(route('patients.lab-reports.urinalysis.store', {
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
    form.put(route('patients.lab-reports.urinalysis.update', {
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
      {{ __('Fecal Analysis') }}
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
          id="collection_method_id"
          class="sm:col-span-2"
          :label="__('Collection Method')"
        >
          <SelectInput
            v-model="form.collection_method_id"
            name="collection_method_id"
            :options="$page.props.options.labUrineCollectionMethodsOptions"
          />
          <InputError
            :message="form.errors.collection_method_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="sg"
          class="sm:col-span-2"
          :label="__('Specific Gravity')"
        >
          <TextInput
            v-model="form.sg"
            name="sg"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.sg"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="color"
          class="sm:col-span-2"
          :label="__('Color')"
        >
          <TextInput
            v-model="form.color"
            name="color"
          />
          <InputError
            :message="form.errors.color"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="turbidity_id"
          class="sm:col-span-2"
          :label="__('Turbidity')"
        >
          <SelectInput
            v-model="form.turbidity_id"
            name="turbidity_id"
            :options="$page.props.options.labUrineTurbiditiesOptions"
          />
          <InputError
            :message="form.errors.turbidity_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="odor_id"
          class="sm:col-span-2"
          :label="__('Odor')"
        >
          <SelectInput
            v-model="form.odor_id"
            name="odor_id"
            :options="$page.props.options.labUrineOdorsOptions"
          />
          <InputError
            :message="form.errors.odor_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="bun"
          class="sm:col-span-2"
          :label="__('BUN')"
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
          id="crystals"
          class="sm:col-span-2"
          :label="__('Crystals')"
        >
          <TextInput
            v-model="form.crystals"
            name="crystals"
          />
          <InputError
            :message="form.errors.crystals"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="casts"
          class="sm:col-span-2"
          :label="__('Casts')"
        >
          <TextInput
            v-model="form.casts"
            name="casts"
          />
          <InputError
            :message="form.errors.casts"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="cells"
          class="sm:col-span-2"
          :label="__('Cells')"
        >
          <TextInput
            v-model="form.cells"
            name="cells"
          />
          <InputError
            :message="form.errors.cells"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="microorganisms"
          class="sm:col-span-2"
          :label="__('Microorganisms')"
        >
          <TextInput
            v-model="form.microorganisms"
            name="microorganisms"
          />
          <InputError
            :message="form.errors.microorganisms"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="ph"
          class="sm:col-span-2"
          :label="__('pH')"
        >
          <TextInput
            v-model="form.ph"
            name="ph"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.ph"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="pro"
          class="sm:col-span-2"
          :label="__('Protein (g/dl)')"
        >
          <TextInput
            v-model="form.pro"
            name="pro"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.pro"
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
          />
          <InputError
            :message="form.errors.glu"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="ket"
          class="sm:col-span-2"
          :label="__('Ketones')"
        >
          <TextInput
            v-model="form.ket"
            name="ket"
          />
          <InputError
            :message="form.errors.ket"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="bili"
          class="sm:col-span-2"
          :label="__('Bilirubin')"
        >
          <TextInput
            v-model="form.bili"
            name="bili"
          />
          <InputError
            :message="form.errors.bili"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="ubg"
          class="sm:col-span-2"
          :label="__('Urobilinogen')"
        >
          <TextInput
            v-model="form.ubg"
            name="ubg"
          />
          <InputError
            :message="form.errors.ubg"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="nitrite"
          class="sm:col-span-2"
          :label="__('Nitrite')"
        >
          <TextInput
            v-model="form.nitrite"
            name="nitrite"
          />
          <InputError
            :message="form.errors.nitrite"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="leuc"
          class="sm:col-span-2"
          :label="__('Leukocytes')"
        >
          <TextInput
            v-model="form.leuc"
            name="leuc"
          />
          <InputError
            :message="form.errors.leuc"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="blood"
          class="sm:col-span-2"
          :label="__('Blood')"
        >
          <TextInput
            v-model="form.blood"
            name="blood"
          />
          <InputError
            :message="form.errors.blood"
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
