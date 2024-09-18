<script setup>
import {useForm} from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
    patientId: {
        type: Number,
        required: true
    },
    location: {
        type: Object,
        default: () => ({})
    },
    show: Boolean,
});

const emit = defineEmits(['close']);

const form = useForm({});

const close = () => emit('close');

const deleteLocation = () => {
    form.delete(route('patients.location.destroy', {
        patient: props.patientId,
        location: props.location.patient_location_id
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
      {{ __('Delete Location From Patient') }}
    </template>
    <template #content>
      <strong>{{ __('Are you sure you want to delete this location?') }}</strong>
      <p class="text-sm mt-4">
        {{ location.location_for_humans }}
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
        @click="deleteLocation"
      >
        {{ __('Delete Location') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>
