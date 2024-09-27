<script setup>
import {useForm} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextareaAutosize from '@/Components/FormElements/TextareaAutosize.vue';
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
  integument: props.necropsy.integument,
  cavities: props.necropsy.cavities,
  cardiovascular: props.necropsy.cardiovascular,
  respiratory: props.necropsy.respiratory,
  gastrointestinal: props.necropsy.gastrointestinal,
  endocrine_reproductive: props.necropsy.endocrine_reproductive,
  liver_gallbladder: props.necropsy.liver_gallbladder,
  hematopoietic: props.necropsy.hematopoietic,
  renal: props.necropsy.renal,
  nervous: props.necropsy.nervous,
  musculoskeletal: props.necropsy.musculoskeletal,
  head: props.necropsy.head,
  integument_finding: props.necropsy.integument_finding || 'Not examined',
  cavities_finding: props.necropsy.cavities_finding || 'Not examined',
  cardiovascular_finding: props.necropsy.cardiovascular_finding || 'Not examined',
  respiratory_finding: props.necropsy.respiratory_finding || 'Not examined',
  gastrointestinal_finding: props.necropsy.gastrointestinal_finding || 'Not examined',
  endocrine_reproductive_finding: props.necropsy.endocrine_reproductive_finding || 'Not examined',
  liver_gallbladder_finding: props.necropsy.liver_gallbladder_finding || 'Not examined',
  hematopoietic_finding: props.necropsy.hematopoietic_finding || 'Not examined',
  renal_finding: props.necropsy.renal_finding || 'Not examined',
  nervous_finding: props.necropsy.nervous_finding || 'Not examined',
  musculoskeletal_finding: props.necropsy.musculoskeletal_finding || 'Not examined',
  head_finding: props.necropsy.head_finding || 'Not examined',
});

const save = () => {
  form.put(route('patients.necropsy.systems.update', {
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
      {{ __('Body Systems') }}
    </template>
    <template #content>
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
          {{ __('Update Body Systems Details') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
