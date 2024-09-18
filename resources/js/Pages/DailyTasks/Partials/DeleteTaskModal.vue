<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import {__} from '@/Composables/Translate';

const route = inject('route');

const props = defineProps({
    schedulable: {
        type: Object,
        required: true
    },
    show: Boolean,
})

const emit = defineEmits(['close']);
const form = useForm({});

const close = () => emit('close');

const destroy = () => {
  form.delete(route('daily-tasks.delete', {
    type: props.schedulable.type,
    id: props.schedulable.id
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
      {{ __('Delete Task From Patient') }}
    </template>
    <template #content>
      <strong class="font-bold text-red-500">{{ __('Are you sure you want to delete this task?') }}</strong>
      <p class="text-sm mt-4">
        {{ schedulable.summary_body }}
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
        {{ __('Delete Task') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>
