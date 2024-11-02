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

  packed_cell_volume: props.labReport.id ? props.labReport.lab_result.packed_cell_volume : '',
  total_solids: props.labReport.id ? props.labReport.lab_result.total_solids : '',
  buffy_coat: props.labReport.id ? props.labReport.lab_result.buffy_coat : '',
  plasma_color: props.labReport.id ? props.labReport.lab_result.plasma_color : '',
  white_blod_cell_count: props.labReport.id ? props.labReport.lab_result.white_blod_cell_count : '',
  white_blod_cell_count_unit_id: props.labReport.id ? props.labReport.lab_result.white_blod_cell_count_unit_id : '',
  segmented_neutrophils: props.labReport.id ? props.labReport.lab_result.segmented_neutrophils : '',
  segmented_neutrophils_unit_id: props.labReport.id ? props.labReport.lab_result.segmented_neutrophils_unit_id : '',
  band_neutrophils: props.labReport.id ? props.labReport.lab_result.band_neutrophils : '',
  band_neutrophils_unit_id: props.labReport.id ? props.labReport.lab_result.band_neutrophils_unit_id : '',
  eosinophils: props.labReport.id ? props.labReport.lab_result.eosinophils : '',
  eosinophils_unit_id: props.labReport.id ? props.labReport.lab_result.eosinophils_unit_id : '',
  basophils: props.labReport.id ? props.labReport.lab_result.basophils : '',
  basophils_unit_id: props.labReport.id ? props.labReport.lab_result.basophils_unit_id : '',
  lymphocytes: props.labReport.id ? props.labReport.lab_result.lymphocytes : '',
  lymphocytes_unit_id: props.labReport.id ? props.labReport.lab_result.lymphocytes_unit_id : '',
  monocytes: props.labReport.id ? props.labReport.lab_result.monocytes : '',
  monocytes_unit_id: props.labReport.id ? props.labReport.lab_result.monocytes_unit_id : '',
  hemoglobin: props.labReport.id ? props.labReport.lab_result.hemoglobin : '',
  mean_corpuscular_volume: props.labReport.id ? props.labReport.lab_result.mean_corpuscular_volume : '',
  mean_corpuscular_hemoglobin: props.labReport.id ? props.labReport.lab_result.mean_corpuscular_hemoglobin : '',
  mean_corpuscular_hemoglobin_concentration: props.labReport.id ? props.labReport.lab_result.mean_corpuscular_hemoglobin_concentration : '',
  erythrocytes: props.labReport.id ? props.labReport.lab_result.erythrocytes : '',
  reticulocytes: props.labReport.id ? props.labReport.lab_result.reticulocytes : '',
  thrombocytes: props.labReport.id ? props.labReport.lab_result.thrombocytes : '',
  polychromasia: props.labReport.id ? props.labReport.lab_result.polychromasia : '',
  anisocytosis: props.labReport.id ? props.labReport.lab_result.anisocytosis : '',
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
    form.post(route('patients.lab-reports.cbc.store', {
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
    form.put(route('patients.lab-reports.cbc.update', {
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
          id="packed_cell_volume"
          class="sm:col-span-2"
          :label="__('Packed Cell Volume')"
        >
          <TextInput
            v-model="form.packed_cell_volume"
            name="packed_cell_volume"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.packed_cell_volume"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="total_solids"
          class="sm:col-span-2"
          :label="__('Total Solids')"
        >
          <TextInput
            v-model="form.total_solids"
            name="total_solids"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.total_solids"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="buffy_coat"
          class="sm:col-span-2"
          :label="__('Buffy Coat')"
        >
          <TextInput
            v-model="form.buffy_coat"
            name="buffy_coat"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.buffy_coat"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="plasma_color"
          class="sm:col-span-2"
          :label="__('Plasma Color')"
        >
          <TextInput
            v-model="form.plasma_color"
            name="plasma_color"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.plasma_color"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="white_blod_cell_count"
          class="sm:col-span-2"
          :label="__('WBC Count')"
        >
          <InputWithUnit
            v-model:text="form.white_blod_cell_count"
            v-model:unit="form.white_blod_cell_count_unit_id"
            name="white_blod_cell_count"
            type="number"
            step="any"
            min="0"
            :units="$page.props.options.labResultQuantityUnitsOptions"
          />
          <InputError
            :message="form.errors.white_blod_cell_count"
            class="mt-2"
          />
          <InputError
            :message="form.errors.white_blod_cell_count_unit_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="segmented_neutrophils"
          class="sm:col-span-2"
          :label="__('Segmented Neutrophils')"
        >
          <InputWithUnit
            v-model:text="form.segmented_neutrophils"
            v-model:unit="form.segmented_neutrophils_unit_id"
            name="segmented_neutrophils"
            type="number"
            step="any"
            min="0"
            :units="$page.props.options.labResultQuantityUnitsOptions"
          />
          <InputError
            :message="form.errors.segmented_neutrophils"
            class="mt-2"
          />
          <InputError
            :message="form.errors.segmented_neutrophils_unit_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="band_neutrophils"
          class="sm:col-span-2"
          :label="__('Band Neutrophils')"
        >
          <InputWithUnit
            v-model:text="form.band_neutrophils"
            v-model:unit="form.band_neutrophils_unit_id"
            name="band_neutrophils"
            type="number"
            step="any"
            min="0"
            :units="$page.props.options.labResultQuantityUnitsOptions"
          />
          <InputError
            :message="form.errors.band_neutrophils"
            class="mt-2"
          />
          <InputError
            :message="form.errors.band_neutrophils_unit_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="eosinophils"
          class="sm:col-span-2"
          :label="__('Eosinophils')"
        >
          <InputWithUnit
            v-model:text="form.eosinophils"
            v-model:unit="form.eosinophils_unit_id"
            name="eosinophils"
            type="number"
            step="any"
            min="0"
            :units="$page.props.options.labResultQuantityUnitsOptions"
          />
          <InputError
            :message="form.errors.eosinophils"
            class="mt-2"
          />
          <InputError
            :message="form.errors.eosinophils_unit_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="basophils"
          class="sm:col-span-2"
          :label="__('Basophils')"
        >
          <InputWithUnit
            v-model:text="form.basophils"
            v-model:unit="form.basophils_unit_id"
            name="basophils"
            type="number"
            step="any"
            min="0"
            :units="$page.props.options.labResultQuantityUnitsOptions"
          />
          <InputError
            :message="form.errors.basophils"
            class="mt-2"
          />
          <InputError
            :message="form.errors.basophils_unit_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="lymphocytes"
          class="sm:col-span-2"
          :label="__('Lymphocytes')"
        >
          <InputWithUnit
            v-model:text="form.lymphocytes"
            v-model:unit="form.lymphocytes_unit_id"
            name="lymphocytes"
            type="number"
            step="any"
            min="0"
            :units="$page.props.options.labResultQuantityUnitsOptions"
          />
          <InputError
            :message="form.errors.lymphocytes"
            class="mt-2"
          />
          <InputError
            :message="form.errors.lymphocytes_unit_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="monocytes"
          class="sm:col-span-2"
          :label="__('Monocytes')"
        >
          <InputWithUnit
            v-model:text="form.monocytes"
            v-model:unit="form.monocytes_unit_id"
            name="monocytes"
            type="number"
            step="any"
            min="0"
            :units="$page.props.options.labResultQuantityUnitsOptions"
          />
          <InputError
            :message="form.errors.monocytes"
            class="mt-2"
          />
          <InputError
            :message="form.errors.monocytes_unit_id"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="hemoglobin"
          class="sm:col-span-2"
          :label="__('Hemoglobin')"
        >
          <TextInput
            v-model="form.hemoglobin"
            name="hemoglobin"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.hemoglobin"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="mean_corpuscular_volume"
          class="sm:col-span-2"
          :label="__('Mean Corpuscular Volume')"
        >
          <TextInput
            v-model="form.mean_corpuscular_volume"
            name="mean_corpuscular_volume"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.mean_corpuscular_volume"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="mean_corpuscular_hemoglobin"
          class="sm:col-span-2"
          :label="__('Mean Corpuscular Hemoglobin')"
        >
          <TextInput
            v-model="form.mean_corpuscular_hemoglobin"
            name="mean_corpuscular_hemoglobin"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.mean_corpuscular_hemoglobin"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="mean_corpuscular_hemoglobin_concentration"
          class="sm:col-span-2"
          :label="__('Mean Corpuscular Hemoglobin Concentration')"
        >
          <TextInput
            v-model="form.mean_corpuscular_hemoglobin_concentration"
            name="mean_corpuscular_hemoglobin_concentration"
            type="number"
            step="any"
            min="0"
          />
          <InputError
            :message="form.errors.mean_corpuscular_hemoglobin_concentration"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="erythrocytes"
          class="sm:col-span-2"
          :label="__('Erythrocytes')"
        >
          <TextInput
            v-model="form.erythrocytes"
            name="erythrocytes"
          />
          <InputError
            :message="form.errors.erythrocytes"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="reticulocytes"
          class="sm:col-span-2"
          :label="__('Reticulocytes')"
        >
          <TextInput
            v-model="form.reticulocytes"
            name="reticulocytes"
          />
          <InputError
            :message="form.errors.reticulocytes"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="thrombocytes"
          class="sm:col-span-2"
          :label="__('Thrombocytes')"
        >
          <TextInput
            v-model="form.thrombocytes"
            name="thrombocytes"
          />
          <InputError
            :message="form.errors.thrombocytes"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="polychromasia"
          class="sm:col-span-2"
          :label="__('Polychromasia')"
        >
          <TextInput
            v-model="form.polychromasia"
            name="polychromasia"
          />
          <InputError
            :message="form.errors.polychromasia"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="anisocytosis"
          class="sm:col-span-2"
          :label="__('Anisocytosis')"
        >
          <TextInput
            v-model="form.anisocytosis"
            name="anisocytosis"
          />
          <InputError
            :message="form.errors.anisocytosis"
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
