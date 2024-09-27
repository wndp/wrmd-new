<script setup>
import {computed} from 'vue';
import {useForm} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import Checkbox from '@/Components/FormElements/Checkbox.vue';
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
  necropsySampleOtherId: {
    type: Number,
    required: true
  },
  enforceRequired: {
    type: Boolean,
    default: false
  }
});

const showOtherSample = computed(() => form.samples_collected.includes(props.necropsySampleOtherId));

const form = useForm({
  samples_collected: props.necropsy.samples_collected || [],
  other_sample: props.necropsy.other_sample,
  morphologic_diagnosis: props.necropsy.morphologic_diagnosis,
  gross_summary_diagnosis: props.necropsy.gross_summary_diagnosis,
});

const save = () => {
  form.put(route('patients.necropsy.summary.update', {
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
      {{ __('Summary') }}
    </template>
    <template #content>
      <FormRow
        id="samples_collected"
        :label="__('Samples Collected')"
        class="col-span-6"
      >
        <div class="flex flex-wrap gap-4">
          <div
            v-for="sample in $page.props.options.necropsySamplesOptions"
            :key="sample.value"
            class="relative flex items-start"
          >
            <div class="flex items-center h-5">
              <Checkbox
                :id="`samples_collected_${sample.value}`"
                v-model="form.samples_collected"
                :name="`samples_collected_${sample.value}`"
                :value="sample.value"
              />
            </div>
            <div class="ml-2 text-sm">
              <label
                :for="`samples_collected_${sample.value}`"
                class="font-medium text-gray-700 whitespace-nowrap"
              >{{ sample.label }}</label>
            </div>
          </div>
        </div>
        <InputError :message="form.errors.samples_collected" />
      </FormRow>
      <FormRow
        v-if="showOtherSample"
        id="other_sample"
        :label="__('Other Sample')"
        class="col-span-6"
      >
        <TextareaInput
          v-model="form.other_sample"
          name="other_sample"
        />
      </FormRow>
      <FormRow
        id="morphologic_diagnosis"
        :label="__('Morphologic Diagnosis')"
        class="col-span-6"
      >
        <TextareaInput
          v-model="form.morphologic_diagnosis"
          name="morphologic_diagnosis"
        />
      </FormRow>
      <FormRow
        id="gross_summary_diagnosis"
        :label="__('Gross Summary Diagnosis')"
        class="col-span-6"
      >
        <TextareaInput
          v-model="form.gross_summary_diagnosis"
          name="gross_summary_diagnosis"
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
          {{ __('Update Summary Details') }}
        </PrimaryButton>
      </div>
    </template>
  </Panel>
</template>
