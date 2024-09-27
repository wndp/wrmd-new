<script setup>
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import TextareaAutosize from '@/Components/FormElements/TextareaAutosize.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputWithUnit from '@/Components/FormElements/InputWithUnit.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import ActionMessage from '@/Components/FormElements/ActionMessage.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import CustomFields from '@/Components/FormElements/CustomFields.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  form: {
    type: Object,
    default: () => ({})
  },
  canSubmit: {
    type: Boolean,
    default: true
  },
  showExamType: {
    type: Boolean,
    default: false
  },
  abnormalBodyPartFindingID: {
    type: Number,
    required: true
  },
  heading: {
    type: String,
    default: null
  },
  formMethod: {
    type: String,
    default: 'put'
  },
  formRoute: {
    type: String,
    default: 'patients.exam.update'
  },
  enforceRequired: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['submitted']);

const updateCorrespondingBodyPartFinding = (bodyPart) => {
  if (props.form[bodyPart].trim() && props.form[`${bodyPart}_finding_id`] === null) {
    props.form[`${bodyPart}_finding_id`] = props.abnormalBodyPartFindingID
  }
}

const doSubmit = () => emit('submitted');
</script>

<template>
  <Panel>
    <template #title>
      {{ heading || __('Exam Form') }}
    </template>
    <template #content>
      <div class="col-span-6 md:grid md:grid-cols-2 md:gap-x-4">
        <div class="space-y-4">
          <FormRow
            v-if="showExamType"
            id="exam_type_id"
            :label="__('Exam Type')"
            :required="enforceRequired"
          >
            <SelectInput
              v-model="form.exam_type_id"
              name="exam_type_id"
              :options="$page.props.options.examTypesOptions"
            />
            <InputError
              :message="form.errors.exam_type_id"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="examiner"
            :label="__('Examiner Name')"
            :required="enforceRequired"
          >
            <TextInput
              v-model="form.examiner"
              name="examiner"
              autoComplete="exams.examiner"
            />
            <InputError
              :message="form.errors.examiner"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="examined_at"
            :label="__('Date')"
            :required="enforceRequired"
          >
            <DatePicker
              id="examined_at"
              v-model="form.examined_at"
              time
            />
            <InputError
              :message="form.errors.examined_at"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="weight"
            :label="__('Weight')"
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
            <InputError
              :message="form.errors.weight"
              class="mt-2"
            />
            <InputError
              :message="form.errors.weight_unit_id"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="dehydration_id"
            :label="__('Dehydration')"
          >
            <SelectInput
              v-model="form.dehydration_id"
              name="dehydration_id"
              :options="$page.props.options.examDehydrationsOptions"
              hasBlankOption
            />
            <InputError
              :message="form.errors.dehydration_id"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="age"
            :label="__('Age')"
          >
            <InputWithUnit
              v-model:text="form.age"
              v-model:unit="form.age_unit_id"
              name="age"
              :units="$page.props.options.taxaClassAgeUnits"
            />
            <InputError
              :message="form.errors.age"
              class="mt-2"
            />
            <InputError
              :message="form.errors.age_unit_id"
              class="mt-2"
            />
          </FormRow>
        </div>
        <div class="space-y-4">
          <FormRow
            id="attitude_id"
            :label="__('Attitude')"
          >
            <SelectInput
              v-model="form.attitude_id"
              name="attitude_id"
              :options="$page.props.options.examAttitudesOptions"
              hasBlankOption
            />
            <InputError
              :message="form.errors.attitude_id"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="sex_id"
            :label="__('Sex')"
          >
            <SelectInput
              v-model="form.sex_id"
              name="sex_id"
              :options="$page.props.options.examSexesOptions"
            />
            <InputError
              :message="form.errors.sex_id"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="mucous_membrane_color_id"
            :label="__('Mucous Membrane Color')"
          >
            <SelectInput
              v-model="form.mucous_membrane_color_id"
              name="mucous_membrane_color_id"
              :options="$page.props.options.examMucusMembraneColorsOptions"
              hasBlankOption
            />
            <InputError
              :message="form.errors.mucous_membrane_color_id"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="mucous_membrane_texture_id"
            :label="__('Mucous Membrane Texture')"
          >
            <SelectInput
              v-model="form.mucous_membrane_texture_id"
              name="mucous_membrane_texture_id"
              :options="$page.props.options.examMucusMembraneTexturesOptions"
              hasBlankOption
            />
            <InputError
              :message="form.errors.mucous_membrane_texture_id"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="body_condition_id"
            :label="__('Body Condition')"
          >
            <SelectInput
              v-model="form.body_condition_id"
              name="body_condition_id"
              :options="$page.props.options.examBodyConditionsOptions"
              hasBlankOption
            />
            <InputError
              :message="form.errors.body_condition_id"
              class="mt-2"
            />
          </FormRow>
          <FormRow
            id="temperature"
            :label="__('Temperature')"
          >
            <InputWithUnit
              v-model:text="form.temperature"
              v-model:unit="form.temperature_unit_id"
              name="temperature"
              type="number"
              step="any"
              min="0"
              :units="$page.props.options.examTemperatureUnitsOptions"
            />
            <InputError
              :message="form.errors.temperature"
              class="mt-2"
            />
            <InputError
              :message="form.errors.temperature_unit_id"
              class="mt-2"
            />
          </FormRow>
        </div>
      </div>
      <CustomFields
        v-if="false"
        v-model:customValues="form.custom_values"
        group="Exam"
        location="Top"
        :enforceRequired="enforceRequired"
        class="col-span-6"
      />
      <FormRow
        v-for="bodyPart in $page.props.options.bodyPartOptions"
        :id="`bodyPart_${bodyPart.value}`"
        :key="bodyPart.value"
        :label="bodyPart.label"
        class="col-span-6 xl:grid xl:grid-cols-6 xl:gap-x-2 xl:items-center"
      >
        <div class="col-span-5 lg:flex lg:items-center">
          <SelectInput
            v-model="form[`${bodyPart.value}_finding_id`]"
            :name="`${bodyPart.value}_finding_id`"
            :options="$page.props.options.examBodyPartFindingsOptions"
            class="mr-2 md:!w-48"
          />
          <TextareaAutosize
            v-model="form[bodyPart.value]"
            :name="bodyPart.value"
            :autoComplete="`exams.${bodyPart.value}`"
            class="mt-2 lg:mt-0"
            @change="updateCorrespondingBodyPartFinding(bodyPart.value)"
          />
        </div>
        <InputError
          :message="form.errors.treatment"
          class="mt-2"
        />
      </FormRow>
      <CustomFields
        v-if="false"
        v-model:customValues="form.custom_values"
        group="Exam"
        location="Bottom"
        :enforceRequired="enforceRequired"
        class="col-span-6"
      />
      <FormRow
        id="treatment"
        :label="__('Treatments')"
        class="col-span-6 xl:grid xl:grid-cols-6 xl:gap-x-2 xl:items-center"
      >
        <TextareaAutosize
          v-model="form.treatment"
          name="treatment"
          autoComplete="exams.treatment"
          class="col-span-5 lg:flex lg:items-center"
        />
        <InputError
          :message="form.errors.treatment"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="nutrition"
        :label="__('Nutrition')"
        class="col-span-6 xl:grid xl:grid-cols-6 xl:gap-x-2 xl:items-center"
      >
        <TextareaAutosize
          v-model="form.nutrition"
          name="nutrition"
          autoComplete="exams.nutrition"
          class="col-span-5 lg:flex lg:items-center"
        />
        <InputError
          :message="form.errors.nutrition"
          class="mt-2"
        />
      </FormRow>
      <FormRow
        id="comments"
        :label="__('Comments')"
        class="col-span-6 xl:grid xl:grid-cols-6 xl:gap-x-2 xl:items-center"
      >
        <TextareaAutosize
          v-model="form.comments"
          name="comments"
          autoComplete="exams.comments"
          class="col-span-5 lg:flex lg:items-center"
        />
        <InputError
          :message="form.errors.comments"
          class="mt-2"
        />
      </FormRow>
    </template>
    <template
      v-if="canSubmit"
      #actions
    >
      <div class="flex items-center justify-end text-right">
        <ActionMessage
          :on="form.isDirty"
          class="mr-3"
        >
          <span class="text-red-600">{{ __('There are unsaved changes.') }}</span>
        </ActionMessage>
        <ActionMessage
          :on="form.recentlySuccessful"
          class="mr-3"
        >
          {{ __('Saved.') }}
        </ActionMessage>
        <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="doSubmit"
        >
          {{ __('Save Exam') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
