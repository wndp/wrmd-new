<script setup>
import {useForm} from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/FormElements/PrimaryButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import CageCardCard from '@/Components/FormCards/CageCardCard.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  data: {
    type: Object,
    required: true
  },
  show: Boolean
});

const emit = defineEmits(['close']);

const form = useForm({
  custom_values: props.data.custom_values || {},
  taxon_id: props.data.taxon_id,
  common_name: props.data.common_name,
  admitted_at: props.data.admitted_at_local,
  morph_id: props.data.morph_id,
  band: props.data.band,
  name: props.data.name,
  reference_number: props.data.reference_number,
  microchip_number: props.data.microchip_number,
});

const close = () => emit('close');

const update = () => {
    form.put(route('patients.cage_card.update', {
      patient: props.data.patient_id
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
      {{ __('Cage Card') }}
    </template>
    <template #content>
      <CageCardCard
        id="cageCardCard"
        :form="form"
        class="-mt-4 -mx-4 shadow-none"
      />
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
        {{ __('Update Cage Card') }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
