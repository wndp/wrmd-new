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
  float_id: props.labReport.id ? props.labReport.lab_result.float_id : '',
  direct_id: props.labReport.id ? props.labReport.lab_result.direct_id : '',
  centrifugation_id: props.labReport.id ? props.labReport.lab_result.centrifugation_id : '',
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
    form.post(route('patients.lab-reports.fecal.store', {
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
    form.put(route('patients.lab-reports.fecal.update', {
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
        <FormRow
          id="float_id"
          class="sm:col-span-2"
          :label="__('Float')"
        >
          <SelectInput
            v-model="form.float_id"
            name="float_id"
            :options="$page.props.options.labBooleanOptions"
            hasBlankOption
          />
          <InputError
            :message="form.errors.float_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="direct_id"
          class="sm:col-span-2"
          :label="__('Direct')"
        >
          <SelectInput
            v-model="form.direct_id"
            name="direct_id"
            :options="$page.props.options.labBooleanOptions"
            hasBlankOption
          />
          <InputError
            :message="form.errors.direct_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="centrifugation_id"
          class="sm:col-span-2"
          :label="__('Centrifugation')"
        >
          <SelectInput
            v-model="form.centrifugation_id"
            name="centrifugation_id"
            :options="$page.props.options.labBooleanOptions"
            hasBlankOption
          />
          <InputError
            :message="form.errors.centrifugation_id"
            class="mt-2"
          />
        </FormRow>
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
