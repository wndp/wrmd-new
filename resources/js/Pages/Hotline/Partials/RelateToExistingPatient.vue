<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import format from 'date-fns/format';
import {__} from '@/Composables/Translate';

const props = defineProps({
  incident: {
    type: Object,
    required: true
  },
  show: Boolean,
});

const emit = defineEmits(['close']);

const caseNumber = ref(null)

const form = useForm({
  case_number: '',
  case_number_confirmation: ''
});

const exCaseNumber = ref(format(new Date, 'yy'));

const close = () => emit('close');

const save = () => {
  form.post(route('hotline.incident.patient.store', {
    incident: props.incident,
  }), {
    preserveScroll: true,
    preserveState: false,
    onError: () => caseNumber.value.focus(),
    onSuccess: () => close(),
  });
};
</script>

<template>
  <DialogModal
    :show="show"
    maxWidth="2xl"
    @close="close"
  >
    <template #title>
      {{ __('Relate to Existing Patient') }}
    </template>
    <template #content>
      <strong class="font-bold">{{ __('Has the incident already been admitted as a patient?') }}</strong>
      <p class="text-sm text-gray-600 mt-4">
        {{ __('If a patient was admitted that represents this incident you can associate them together.') }}
      </p>
      <div class="space-y-4 sm:space-y-2 text-left mt-4">
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="case_number"
            class="col-span-2 sm:text-right"
          >
            {{ __('Patient Case Number') }}
          </InputLabel>
          <div class="col-span-3 mt-1 sm:mt-0">
            <TextInput
              ref="caseNumber"
              v-model="form.case_number"
              name="case_number"
              :placeholder="`ex: ${exCaseNumber}-1234`"
            />
            <InputError
              :message="form.errors.case_number"
              class="mt-2"
            />
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="case_number_confirmation"
            class="col-span-2 sm:text-right"
          >
            {{ __('Confirm Case Number') }}
          </InputLabel>
          <div class="col-span-3 mt-1 sm:mt-0">
            <TextInput
              v-model="form.case_number_confirmation"
              name="case_number_confirmation"
            />
          </div>
        </div>
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
        {{ __('Relate Incident to Patient') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
