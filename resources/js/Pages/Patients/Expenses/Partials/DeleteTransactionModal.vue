<template>
  <ConfirmationModal
    :show="show"
    max-width="2xl"
    @close="close"
  >
    <template #title>
      {{ __('Delete Transaction') }}
    </template>
    <template #content>
      <strong class="font-bold text-red-500">{{ __('Are you sure you want to delete this transaction?') }}</strong>
      <p class="text-sm mt-4">
        <strong class="font-bold">Date</strong> {{ transaction.transacted_at_for_humans }}
      </p>
      <p class="text-sm mt-1">
        <strong class="font-bold">Category</strong> {{ transaction.category.name }}
      </p>
      <p
        v-if="transaction.credit"
        class="text-sm mt-1"
      >
        <strong class="font-bold">Credit</strong> {{ transaction.credit_for_humans }}
      </p>
      <p
        v-if="transaction.debit"
        class="text-sm mt-1"
      >
        <strong class="font-bold">Debit</strong> {{ transaction.debit_for_humans }}
      </p>
      <p class="mt-1 text-sm text-gray-500">
        {{ transaction.memo || '' }}
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
        {{ __('Delete Transaction') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>

<script setup>
import { inject, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';

const route = inject('route');

const props = defineProps({
    transaction: {
        type: Object,
        required: true
    },
    show: Boolean,
})

const emit = defineEmits(['close']);
const form = useForm({});
let patient = computed(() => usePage().props.admission.patient);

const close = () => {
  emit('close');
};

const destroy = () => {
  form.delete(route('patients.expenses.destroy', {
    patient: patient.value.id,
    transaction: props.transaction.id
  }), {
    preserveScroll: true,
    onSuccess: () => close()
  });
};

</script>
