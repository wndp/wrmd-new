<script setup>
import { formatISO9075 } from 'date-fns';
import DialogModal from '@/Components/DialogModal.vue';
import FormRow from '@/Components/FormElements/FormRow.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import InputWithUnit from '@/Components/FormElements/InputWithUnit.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  patient: {
      type: Object,
      required: true
  },
  title: {
      type: String,
      required: true
  },
  show: Boolean
});

const emit = defineEmits(['close']);

const form = useForm({
  date_care_at: formatISO9075(new Date()),
  weight: '',
  weight_unit_id: '',
  comments: ''
});

const close = () => emit('close');

const save = () => {
    form.post(route('patients.treatment_log.store', {
        patient: props.patient
    }), {
        preserveScroll: true,
        onSuccess: () => {
          form.reset();
          close();
        }
    });
};
</script>

<template>
  <DialogModal
    :show="show"
    @close="close"
  >
    <template #title>
      {{ title }}
    </template>
    <template #content>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-6">
        <FormRow
          id="treated_at"
          class="sm:col-span-3"
          :label="__('Date')"
        >
          <DatePicker
            v-model="form.treated_at"
            name="treated_at"
            time
          />
          <InputError
            :message="form.errors.treated_at"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="weight"
          class="sm:col-span-3"
          :label="__('Weight')"
        >
          <InputWithUnit
            v-model:text="form.weight"
            v-model:unit="form.weight_unit_id"
            name="edit_weight"
            type="number"
            step="any"
            min="0"
            :units="$page.props.options.weightUnitOptions"
          />
          <InputError
            :message="form.errors.weight"
            class="mt-2"
          />
        </FormRow>
        <FormRow
          id="comments"
          class="sm:col-span-6"
          :label="__('Comments')"
        >
          <Textarea
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
        {{ __('Save Comment') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
