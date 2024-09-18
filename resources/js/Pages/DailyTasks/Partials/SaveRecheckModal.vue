<script setup>
import {useForm, usePage} from '@inertiajs/vue3';
import { formatISO9075 } from 'date-fns';
import DialogModal from '@/Components/DialogModal.vue';
import Alert from '@/Components/Alert.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import SelectInput from '@/Components/FormElements/SelectInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  patientId: {
      type: Number,
      required: true
  },
  recheck: {
      type: Object,
      default: () => { return {} }
  },
  title: {
      type: String,
      required: true
  },
  show: Boolean
})

const emit = defineEmits(['close']);

const form = useForm({
    recheck_start_at: props.recheck.id ? props.recheck.recheck_start_at : formatISO9075(new Date()),
    recheck_end_at: props.recheck.id ? props.recheck.recheck_end_at : formatISO9075(new Date()),
    frequency_id: props.recheck.id ? props.recheck.frequency_id : usePage().props.dailyTaskOptionUiBehaviorIds.singleDoseId,
    assigned_to_id: props.recheck.id ? props.recheck.assigned_to_id : usePage().props.dailyTaskOptionUiBehaviorIds.veterinarianId,
    description: props.recheck.id ? props.recheck.description : ''
});

const close = () => emit('close');

const save = () => {
    if (props.recheck.id) {
        update();
        return;
    }
    store();
};

const store = () => {
    form.post(route('patients.recheck.store', {
        patient: props.patientId
    }), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            close();
        }
    });
};

const update = () => {
    form.put(route('patients.recheck.update', {
        patient: props.patientId,
        recheck: props.recheck
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
      {{ title }}
    </template>
    <template #content>
      <Alert
        v-if="recheck.id"
        color="red"
        class="mb-4"
      >
        {{ __("Warning: Altering a recheck's dates or description may delete any marked-off tasks.") }}
      </Alert>
      <div class="space-y-4 sm:space-y-2">
        <div class="grid grid-cols-6 gap-2 sm:items-center">
          <div class="col-span-3 sm:grid sm:grid-cols-3 sm:gap-x-2 sm:items-center">
            <InputLabel
              for="recheck_start_at"
              class="sm:text-right"
            >
              {{ __('Due Date') }}
            </InputLabel>
            <div class="col-span-2 mt-1 sm:mt-0">
              <DatePicker
                id="recheck_start_at"
                v-model="form.recheck_start_at"
              />
              <InputError
                :message="form.errors.recheck_start_at"
                class="mt-2"
              />
            </div>
          </div>
          <div class="col-span-3 sm:grid sm:grid-cols-3 sm:gap-x-2 sm:items-center">
            <InputLabel
              for="recheck_end_at"
              class="sm:text-right"
            >
              {{ __('Due No Later Than') }}
            </InputLabel>
            <div class="col-span-2 mt-1 sm:mt-0">
              <DatePicker
                id="recheck_end_at"
                v-model="form.recheck_end_at"
              />
              <InputError
                :message="form.errors.recheck_end_at"
                class="mt-2"
              />
            </div>
          </div>
        </div>
        <div class="grid grid-cols-6 gap-2 sm:items-center">
          <div class="col-span-3 sm:grid sm:grid-cols-3 sm:gap-x-2 sm:items-center">
            <InputLabel
              for="frequency_id"
              class="sm:text-right"
            >
              {{ __('Frequency') }}
            </InputLabel>
            <div class="col-span-2 mt-1 sm:mt-0">
              <SelectInput
                v-model="form.frequency_id"
                name="frequency_id"
                :options="$page.props.options.dailyTaskFrequenciesOptions"
              />
              <InputError
                :message="form.errors.frequency_id"
                class="mt-2"
              />
            </div>
          </div>
          <div class="col-span-3 sm:grid sm:grid-cols-3 sm:gap-x-2 sm:items-center">
            <InputLabel
              for="assigned_to_id"
              class="sm:text-right"
            >
              {{ __('Assigned To') }}
            </InputLabel>
            <div class="col-span-2 mt-1 sm:mt-0">
              <SelectInput
                v-model="form.assigned_to_id"
                name="assigned_to_id"
                :options="$page.props.options.dailyTaskAssignmentsOptions"
              />
              <InputError
                :message="form.errors.assigned_to_id"
                class="mt-2"
              />
            </div>
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="description"
            class="sm:text-right"
          >
            {{ __('Description') }}
          </InputLabel>
          <div class="col-span-5 mt-1 sm:mt-0">
            <TextareaInput
              v-model="form.description"
              name="description"
            />
            <InputError
              :message="form.errors.description"
              class="mt-2"
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
        {{ __('Save Recheck') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
