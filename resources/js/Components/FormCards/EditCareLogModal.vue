<script setup>
import {useForm} from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import InputWithUnit from '@/Components/FormElements/InputWithUnit.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';

const props = defineProps({
  patient: {
    type: Object,
    required: true
  },
  log: {
    type: Object,
    required: true
  },
  show: Boolean,
});

const emit = defineEmits(['close']);

const form = useForm({
  treated_at: props.log.treated_at,
  weight: props.log.weight,
  weight_unit: props.log.weight_unit,
  comments: props.log.comments
});

const close = () => emit('close');

const update = () => {
  form.put(route('patients.treatment_log.update', {
    patient: props.patient,
    treatment_log: props.log
  }), {
    preserveScroll: true,
    onSuccess: () => close()
  });
};
</script>

<template>
  <DialogModal
    :show="show"
    @close="close"
  >
    <template #title>
      {{ __('Update Treatment Log') }}
    </template>
    <template #content>
      <div class="space-y-2">
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="treated_at"
            class="sm:text-right"
          >
            {{ __('Author') }}
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <p class="text-sm text-gray-700">
              {{ log.user.name }}
            </p>
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="treated_at"
            class="sm:text-right"
          >
            {{ __('Date') }}
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <DatePicker
              id="edit_treated_at"
              v-model="form.treated_at"
              time
            />
            <InputError
              :message="form.errors.treated_at"
              class="mt-2"
            />
          </div>
          <InputLabel
            for="weight"
            class="sm:text-right mt-2 sm:mt-0"
          >
            {{ __('Weight') }}
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <InputWithUnit
              v-model:text="form.weight"
              v-model:unit="form.weight_unit"
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
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="edit_comments"
            class="sm:text-right"
          >
            {{ __('Comments') }}
          </InputLabel>
          <div class="col-span-5 mt-1 sm:mt-0">
            <TextareaInput
              v-model="form.comments"
              name="edit_comments"
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
        @click="update"
      >
        {{ __('Update Treatment Log') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
