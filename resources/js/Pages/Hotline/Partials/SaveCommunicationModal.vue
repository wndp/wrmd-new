<script setup>
import {useForm, usePage} from '@inertiajs/vue3';
import { formatISO9075 } from 'date-fns';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/FormElements/InputLabel.vue';
import TextInput from '@/Components/FormElements/TextInput.vue';
import TextareaInput from '@/Components/FormElements/TextareaInput.vue';
import DatePicker from '@/Components/FormElements/DatePicker.vue';
import InputError from '@/Components/FormElements/InputError.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  incident: {
    type: Object,
    required: true
  },
  communication: {
      type: Object,
      default: () => ({})
  },
  show: Boolean,
});

const emit = defineEmits(['close']);

const form = useForm({
  communication_at: props.communication.id ? props.communication.communication_at_local : formatISO9075(new Date()),
  communication_by: props.communication.id ? props.communication.communication_by : usePage().props.auth.user.name,
  communication: props.communication.id ? props.communication.communication : ''
});

const close = () => emit('close');

const save = () => {
  if (props.communication.id) {
      update();
      return;
  }
  store();
}

const store = () => {
    form.post(route('hotline.incident.communications.store', {
        incident: props.incident
    }), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            close();
        }
    });
};

const update = () => {
    form.put(route('hotline.incident.communications.update', {
        incident: props.incident,
        communication: props.communication
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
      {{ communication.id ? __('Update Communication') : __('Create Communication') }}
    </template>
    <template #content>
      <div class="space-y-2">
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="communication_at"
            class="sm:text-right"
          >
            {{ __('Date') }}
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <DatePicker
              id="communication_at"
              v-model="form.communication_at"
              time
            />
            <InputError
              :message="form.errors.communication_at"
              class="mt-2"
            />
          </div>
          <InputLabel
            for="communication_by"
            class="sm:text-right"
          >
            {{ __('By') }}
          </InputLabel>
          <div class="col-span-2 mt-1 sm:mt-0">
            <TextInput
              v-model="form.communication_by"
              name="communication_by"
            />
            <InputError
              :message="form.errors.method"
              class="mt-2"
            />
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-6 sm:gap-x-2 sm:items-center">
          <InputLabel
            for="communication"
            class="sm:text-right"
          >
            {{ __('Value') }}
          </InputLabel>
          <div class="col-span-5 mt-1 sm:mt-0">
            <TextareaInput
              v-model="form.communication"
              name="communication"
            />
            <InputError
              :message="form.errors.communication"
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
        {{ __('Save Communication') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
