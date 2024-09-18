<script setup>
import {useForm} from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  incident: {
    type: Object,
    required: true
  },
  communication: {
      type: Object,
      required: true
  },
  show: Boolean,
});

const emit = defineEmits(['close']);

const form = useForm({});

const close = () => emit('close');

const doDelete = () => {
   form.delete(route('hotline.incident.communications.destroy', {
      incident: props.incident,
      communication: props.communication
  }), {
      preserveState: false,
      onSuccess: () => close()
  });
};
</script>

<template>
  <ConfirmationModal
    :show="show"
    maxWidth="2xl"
    @close="close"
  >
    <template #title>
      {{ __('Delete Communication') }}
    </template>
    <template #content>
      <strong>{{ __('Are you sure you want to delete this communication?') }}</strong>
      <p class="text-sm text-gray-600 mt-4">
        {{ communication.communication_for_humans }}
      </p>
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        {{ __('Nevermind') }}
      </SecondaryButton>
      <DangerButton
        class="ml-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="doDelete"
      >
        {{ __('Delete Communication') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>
