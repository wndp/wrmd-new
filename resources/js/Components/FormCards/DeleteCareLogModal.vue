<script setup>
import {useForm} from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

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

const form = useForm({});

const close = () => emit('close');

const destroy = () => {
    form.delete(route('patients.care_log.destroy', {
        patient: props.patient.id,
        care_log: props.log.id
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
      {{ __('Delete Care Log From Patient') }}
    </template>
    <template #content>
      <div dusk="delete-care-log-modal">
        <strong class="font-bold text-red-500">{{ __('Are you sure you want to delete this care log?') }}</strong>
        <h4 class="text-base font-bold mt-4">{{ log.logged_at_for_humans }}</h4>
        <p class="text-sm whitespace-pre-line mt-1">
          {{ log.body }}
        </p>
      </div>
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
        {{ __('Delete Care Log') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>
