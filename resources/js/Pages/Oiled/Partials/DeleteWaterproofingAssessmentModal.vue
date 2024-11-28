<script setup>
import {inject} from 'vue';
import {useForm} from '@inertiajs/vue3';
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
  assessment: {
    type: Object,
    required: true
  },
  show: Boolean,
})

const emit = defineEmits(['close']);
const form = useForm({});

const close = () => emit('close');

const destroy = () => {
  form.delete(route('oiled.waterproofing_assessment.destroy', {
    patient: props.patientId,
    assessment: props.assessment.id
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
      {{ __('Delete Waterproofing Assessment') }}
    </template>
    <template #content>
      <strong class="font-bold text-red-500">{{ __('Are you sure you want to delete this waterproofing assessment?') }}</strong>
      <p class="text-sm mt-4">
        <strong class="font-bold">Date</strong> {{ assessment.evaluated_at_for_humans }}
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
        {{ __('Delete Assessment') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>
