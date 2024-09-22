<script setup>
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import CommonName from '@/Components/FormElements/CommonName.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import range from 'lodash/range';
import {__} from '@/Composables/Translate';

defineProps({
  form: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['caseYearChange', 'update:modelValue']);

let casesToCreate = range(1, 51);
</script>

<template>
  <Panel class="mt-8">
    <template #content>
      <FormRow
        id="case_year"
        :label="__('Case Year')"
        required
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.case_year"
          name="case_year"
          :options="$page.props.options.availableYears"
          required
          @change="emit('caseYearChange', form.case_year)"
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
        required
        class="col-span-6 md:col-span-2"
      >
        <DatePicker
          id="admitted_at"
          v-model="form.admitted_at"
          time
          required
        />
        <InputError
          id="admitted_at_error"
          class="mt-2"
          :message="form.errors?.admitted_at"
        />
      </FormRow>
      <FormRow
        id="common_name"
        :label="__('Common Name')"
        required
        class="col-span-6 md:col-start-1 md:col-span-4"
      >
        <CommonName
          id="common_name"
          v-model="form.common_name"
          required
        />
        <InputError
          id="common_name_error"
          class="mt-2"
          :message="form.errors?.common_name"
        />
      </FormRow>
      <FormRow
        id="morph_id"
        :label="__('Morph')"
        class="col-span-6 md:col-span-2"
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
        id="cases_to_create"
        :label="__('Number of Patients')"
        required
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.cases_to_create"
          name="cases_to_create"
          :options="casesToCreate"
        />
        <InputError
          id="cases_to_create_error"
          class="mt-2"
          :message="form.errors?.cases_to_create"
        />
      </FormRow>
      <FormRow
        id="reference_number"
        :label="__('Reference Number')"
        class="col-span-6 md:col-span-2"
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
        :label="__('Microchip Number')"
        class="col-span-6 md:col-span-2"
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
    </template>
  </Panel>
</template>
