<script setup>
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import CommonName from '@/Components/FormElements/CommonName.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import CustomFields from '@/Components/FormElements/CustomFields.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  form: {
    type: Object,
    default: () => ({})
  },
  enforceRequired: {
    type: Boolean,
    default: true
  }
});

const commonNameSelected = (selected) => {
  props.form.taxon_id = selected?.value
  console.log(selected)
}
</script>

<template>
  <Panel>
    <template #heading>
      {{ __('Cage Card') }}
    </template>
    <template #content>
      <FormRow
        id="common_name"
        class="col-span-6"
        :label="__('Common Name')"
      >
        <CommonName
          id="common_name"
          v-model="form.common_name"
          @select="commonNameSelected"
        />
      </FormRow>
      <CustomFields
        v-if="false"
        v-model:customValues="form.custom_values"
        group="Patient"
        panel="Cage Card"
        location="Top"
        :enforceRequired="enforceRequired"
      />
      <FormRow
        id="admitted_at"
        class="col-span-6 md:col-span-3"
        :label="__('Date Admitted')"
      >
        <DatePicker
          id="admitted_at"
          v-model="form.admitted_at"
          :time="true"
        />
        <InputError
          id="admitted_at_error"
          class="mt-2"
          :message="form.errors?.admitted_at"
        />
      </FormRow>
      <FormRow
        id="morph_id"
        class="col-span-6 md:col-span-3"
        :label="__('Morph')"
      >
        <SelectInput
          v-model="form.morph_id"
          name="morph_id"
          :options="$page.props.options.taxaMorphsOptions"
          hasBlankOption
        />
        <InputError
          id="morph_id_error"
          class="mt-2"
          :message="form.errors?.morph_id"
        />
      </FormRow>
      <FormRow
        id="band"
        class="col-span-6 md:col-span-3"
        :label="__('Band')"
      >
        <TextInput
          v-model="form.band"
          name="band"
        />
        <InputError
          id="band_error"
          class="mt-2"
          :message="form.errors?.band"
        />
      </FormRow>
      <FormRow
        id="name"
        class="col-span-6 md:col-span-3"
        :label="__('Name')"
      >
        <TextInput
          v-model="form.name"
          name="name"
        />
        <InputError
          id="name_error"
          class="mt-2"
          :message="form.errors?.name"
        />
      </FormRow>
      <FormRow
        id="reference_number"
        class="col-span-6 md:col-span-3"
        :label="__('Reference #')"
      >
        <TextInput
          v-model="form.reference_number"
          name="reference_number"
        />
        <InputError
          id="reference_number_error"
          class="mt-2"
          :message="form.errors?.reference_number"
        />
      </FormRow>
      <FormRow
        id="microchip_number"
        class="col-span-6 md:col-span-3"
        :label="__('Microchip #')"
      >
        <TextInput
          v-model="form.microchip_number"
          name="microchip_number"
        />
        <InputError
          id="microchip_number_error"
          class="mt-2"
          :message="form.errors?.microchip_number"
        />
      </FormRow>
      <CustomFields
        v-if="false"
        v-model:customValues="form.custom_values"
        group="Patient"
        panel="Cage Card"
        location="Bottom"
        :enforceRequired="enforceRequired"
      />
    </template>
  </Panel>
</template>
