<script setup>
import {useForm} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  patientId: {
    type: String,
    required: true
  },
  banding: {
    type: Object,
    default: () => ({})
  },
  enforceRequired: {
    type: Boolean,
    default: true
  }
});

const form = useForm({
  band_number: props.banding?.band_number,
  banded_at: props.banding?.banded_at,
  age_code_id: props.banding?.age_code_id,
  how_aged_id: props.banding?.how_aged_id,
  sex_code_id: props.banding?.sex_code_id,
  how_sexed_id: props.banding?.how_sexed_id,
  status_code_id: props.banding?.status_code_id,
  additional_status_code_id: props.banding?.additional_status_code_id,
  band_size_id: props.banding?.band_size_id,
  master_bander_number: props.banding?.master_bander_number,
  banded_by: props.banding?.banded_by,
  location_number: props.banding?.location_number,
  band_disposition_id: props.banding?.band_disposition_id,
  remarks: props.banding?.remarks,
});

const save = () => {
  form.put(route('patients.banding-morphometrics.banding.update', {
    patient: props.patientId
  }), {
    preserveScroll: true,
    //onError: () => this.stopAutoSave()
  });
};
</script>

<template>
  <Panel>
    <template #title>
      {{ __('Banding') }}
    </template>
    <template #content>
      <FormRow
        id="banded_at"
        :label="__('Banding Date')"
        :required="enforceRequired"
        class="col-span-6 md:col-span-2"
      >
        <DatePicker
          id="banded_at"
          v-model="form.banded_at"
          :required="enforceRequired"
        />
        <InputError :message="form.errors.banded_at" />
      </FormRow>
      <FormRow
        id="band_number"
        :label="__('Band Number')"
        :required="enforceRequired"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.band_number"
          name="band_number"
          :required="enforceRequired"
        />
        <InputError :message="form.errors.band_number" />
      </FormRow>
      <FormRow
        id="band_disposition_id"
        :label="__('Band Disposition')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.band_disposition_id"
          name="band_disposition_id"
          :options="$page.props.options.bandingDispositionCodesOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.band_disposition_id" />
      </FormRow>
      <FormRow
        id="age_code_id"
        :label="__('Age Code')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.age_code_id"
          name="age_code_id"
          :options="$page.props.options.bandingAgeCodesOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.age_code_id" />
      </FormRow>
      <FormRow
        id="how_aged_id"
        :label="__('How Aged')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.how_aged_id"
          name="how_aged_id"
          :options="$page.props.options.bandingHowAgedCodesOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.how_aged_id" />
      </FormRow>
      <FormRow
        id="location_number"
        :label="__('BandIt Location ID')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.location_number"
          name="location_number"
        />
        <InputError :message="form.errors.location_number" />
      </FormRow>
      <FormRow
        id="sex_code_id"
        :label="__('Sex Code')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.sex_code_id"
          name="sex_code_id"
          :options="$page.props.options.bandingSexCodesOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.sex_code_id" />
      </FormRow>
      <FormRow
        id="how_sexed_id"
        :label="__('How Sexed')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.how_sexed_id"
          name="how_sexed_id"
          :options="$page.props.options.bandingHowSexedCodesOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.how_sexed_id" />
      </FormRow>
      <FormRow
        id="band_size_id"
        :label="__('Band Size')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.band_size_id"
          name="band_size_id"
          :options="$page.props.options.bandingBandSizesOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.band_size_id" />
      </FormRow>
      <FormRow
        id="status_code_id"
        :label="__('Status Code')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.status_code_id"
          name="status_code_id"
          :options="$page.props.options.bandingStatusCodesOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.status_code_id" />
      </FormRow>
      <FormRow
        id="additional_status_code_id"
        :label="__('Additional Status Code')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.additional_status_code_id"
          name="additional_status_code_id"
          :options="$page.props.options.bandingAdditionalInformationStatusCodesOptions"
          hasBlankOption
        />
        <InputError :message="form.errors.additional_status_code_id" />
      </FormRow>
      <FormRow
        id="master_bander_number"
        :label="__('Master Bander ID')"
        class="col-span-6 md:col-span-2 md:col-start-1"
      >
        <TextInput
          v-model="form.master_bander_number"
          name="master_bander_number"
        />
        <InputError :message="form.errors.master_bander_number" />
      </FormRow>
      <FormRow
        id="banded_by"
        :label="__('Banded By')"
        class="col-span-6 md:col-span-2"
      >
        <TextInput
          v-model="form.banded_by"
          name="banded_by"
        />
        <InputError :message="form.errors.banded_by" />
      </FormRow>
      <FormRow
        id="remarks"
        :label="__('Remarks')"
        class="col-span-6"
      >
        <TextInput
          v-model="form.remarks"
          name="remarks"
        />
        <InputError :message="form.errors.remarks" />
      </FormRow>
    </template>
    <template #actions>
      <div class="flex items-center justify-end text-right">
        <ActionMessage
          :on="form.isDirty"
          class="mr-3"
        >
          <span class="text-red-600">{{ __('There are unsaved changes') }}</span>
        </ActionMessage>
        <ActionMessage
          :on="form.recentlySuccessful"
          class="mr-3"
        >
          {{ __('Saved') }}
        </ActionMessage>
        <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="save"
        >
          {{ __('Update Banding Data') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
