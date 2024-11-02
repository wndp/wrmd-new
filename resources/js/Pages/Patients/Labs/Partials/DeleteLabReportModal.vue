<script setup>
import {inject} from 'vue';
import {useForm} from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const route = inject('route');

const props = defineProps({
    labReport: {
        type: Object,
        required: true
    },
    patientId: {
      type: String,
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
  form.delete(route('patients.lab-reports.delete', {
    patient: props.patientId,
    labReport: props.labReport.id
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
      {{ __('Delete Lab Result') }}
    </template>
    <template #content>
      <strong class="font-bold text-red-500">{{ __('Are you sure you want to delete this lab result?') }}</strong>
      <p class="text-sm mt-4">
        <strong class="font-bold">{{ __('Date') }}</strong> {{ labReport.analysis_date_at_for_humans }}
      </p>
      <p class="text-sm mt-1">
        <strong class="font-bold">{{ __('Analysis') }}</strong> {{ labReport.badge_text }}
      </p>
      <p class="text-sm mt-1">
        <strong class="font-bold">{{ __('Comments') }}</strong> {{ labReport.comments }}
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
        {{ __('Delete Lab Result') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>
