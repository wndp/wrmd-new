<script setup>
import Panel from '@/Components/Panel.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import CommonName from '@/Components/FormElements/CommonName.vue';
import RequiredInput from '@/Components/FormElements/RequiredInput.vue';
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
      <div class="col-span-6 md:col-span-2">
        <InputLabel for="case_year">
          {{ __('Case Year') }}
          <RequiredInput />
        </InputLabel>
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
      </div>
      <div class="col-span-6 md:col-span-2">
        <InputLabel for="admitted_at">
          {{ __('Date Admitted') }}
          <RequiredInput />
        </InputLabel>
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
      </div>
      <div class="col-span-6 md:col-start-1 md:col-span-4">
        <InputLabel for="common_name">
          {{ __('Common Name') }}
          <RequiredInput />
        </InputLabel>
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
      </div>
      <div class="col-span-6 md:col-span-2">
        <InputLabel for="morph_id">
          {{ __('Morph') }}
        </InputLabel>
        <SelectInput
          v-model="form.morph_id"
          name="morph_id"
          :options="$page.props.options.taxaMorphsOptions"
        />
        <InputError
          id="morph_id_error"
          class="mt-2"
          :message="form.errors?.morph_id"
        />
      </div>
      <div class="col-span-6 md:col-span-2">
        <InputLabel for="cases_to_create">
          {{ __('Number of Patients') }}
          <RequiredInput />
        </InputLabel>
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
      </div>
      <div class="col-span-6 md:col-span-2">
        <InputLabel for="reference_number">
          {{ __('Reference Number') }}
        </InputLabel>
        <TextInput
          v-model="form.reference_number"
          name="reference_number"
        />
        <InputError
          id="reference_number_error"
          class="mt-2"
          :message="form.errors?.reference_number"
        />
      </div>
      <div class="col-span-6 md:col-span-2">
        <InputLabel for="microchip_number">
          {{ __('Microchip Number') }}
        </InputLabel>
        <TextInput
          v-model="form.microchip_number"
          name="microchip_number"
        />
        <InputError
          id="microchip_number_error"
          class="mt-2"
          :message="form.errors?.microchip_number"
        />
      </div>
    </template>
  </Panel>
</template>
