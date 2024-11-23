<script setup>
import { inject, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const route = inject('route');

const props = defineProps({
   patientId: {
    type: String,
    required: true
  },
  conditioning: {
    type: Object,
    required: true
  },
  show: Boolean,
})

const emit = defineEmits(['close']);
const form = useForm({});

const close = () => {
  emit('close');
};

const destroy = () => {
  form.delete(route('oiled.conditioning.destroy', {
    patient: props.patientId,
    conditioning: props.conditioning.id
  }), {
    preserveScroll: true,
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
      {{ __('Delete Conditioning') }}
    </template>
    <template #content>
      <strong class="font-bold text-red-500">{{ __('Are you sure you want to delete this conditioning record?') }}</strong>
      <p class="text-sm mt-4">
        <strong class="font-bold">Date</strong> {{ conditioning.evaluated_at_for_humans }}
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
        @click="destroy"
      >
        {{ __('Delete Conditioning') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>
