<script setup>
import {useForm} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputWithUnit from '@/Components/FormElements/InputWithUnit.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  patientId: {
    type: String,
    required: true
  },
  necropsy: {
    type: Object,
    required: true
  },
  enforceRequired: {
    type: Boolean,
    default: false
  }
});

const form = useForm({
  weight: props.necropsy.weight,
  weight_unit: props.necropsy.weight_unit,
  age: props.necropsy.age,
  age_unit: props.necropsy.age_unit,
  sex: props.necropsy.sex,
  bcs: props.necropsy.bcs,
  wing: props.necropsy.wing,
  tarsus: props.necropsy.tarsus,
  culmen: props.necropsy.culmen,
  exposed_culmen: props.necropsy.exposed_culmen,
  bill_depth: props.necropsy.bill_depth,
});

const save = () => {
  form.put(route('patients.necropsy.morphometrics.update', {
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
      {{ __('Morphometrics') }}
    </template>
    <template #content>
      <FormRow
        id="weight"
        :label="__('Weight')"
        class="col-span-6 md:col-span-2"
      >
        <InputWithUnit
          v-model:text="form.weight"
          v-model:unit="form.weight_unit_id"
          name="weight"
          type="number"
          step="any"
          min="0"
          :units="$page.props.options.examWeightUnitsOptions"
        />
        <InputError :message="form.errors.weight_unit_id" />
      </FormRow>
      <FormRow
        id="age"
        :label="__('Age')"
        class="col-span-6 md:col-span-2"
      >
        <InputWithUnit
          v-model:text="form.age"
          v-model:unit="form.age_unit_id"
          name="age"
          type="number"
          step="any"
          min="0"
          :units="[]"
        />
        <InputError :message="form.errors.age_unit_id" />
      </FormRow>
      <FormRow
        id="sex"
        :label="__('Sex')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.sex_id"
          name="sex_id"
          :options="$page.props.options.examSexesOptions"
        />
        <InputError :message="form.errors.sex_id" />
      </FormRow>
      <FormRow
        id="body_condition_id"
        :label="__('Body Condition')"
        class="col-span-6 md:col-span-2"
      >
        <SelectInput
          v-model="form.body_condition_id"
          name="body_condition_id"
          :options="$page.props.options.examBodyConditionsOptions"
        />
        <InputError :message="form.errors.body_condition_id" />
      </FormRow>
      <FormRow
        id="wing"
        :label="__('Unflat Wing')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex">
          <TextInput
            v-model="form.wing"
            name="wing"
            type="number"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.wing" />
      </FormRow>
      <FormRow
        id="tarsus"
        :label="__('Tarsus')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex">
          <TextInput
            v-model="form.tarsus"
            name="tarsus"
            type="number"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.tarsus" />
      </FormRow>
      <FormRow
        id="culmen"
        :label="__('Culmen')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex">
          <TextInput
            v-model="form.culmen"
            name="culmen"
            type="number"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.culmen" />
      </FormRow>
      <FormRow
        id="exposed_culmen"
        :label="__('Exposed Culmen')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex">
          <TextInput
            v-model="form.exposed_culmen"
            name="exposed_culmen"
            type="number"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.exposed_culmen" />
      </FormRow>
      <FormRow
        id="bill_depth"
        :label="__('Bill Depth')"
        class="col-span-6 md:col-span-2"
      >
        <div class="flex">
          <TextInput
            v-model="form.bill_depth"
            name="bill_depth"
            type="number"
          />
          <span class="text-gray-600 text-sm ml-2">(mm)</span>
        </div>
        <InputError :message="form.errors.bill_depth" />
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
          {{ __('Update Morphometrics Details') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
