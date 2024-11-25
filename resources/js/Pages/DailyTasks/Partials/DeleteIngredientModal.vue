<script setup lang="ts">
import {defineEmits} from 'vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import {__} from '@/Composables/Translate';

defineProps({
    show: Boolean,
});

const emit = defineEmits([
    'confirmDelete',
    'close'
]);

const close = () => emit('close');
const confirmDelete = () => emit('confirmDelete');
</script>

<template>
  <ConfirmationModal
    :show="show"
    maxWidth="2xl"
    @close="close"
  >
    <template #title>
      {{ __('Delete Ingredient?') }}
    </template>
    <template #content>
      <strong>{{ __('Are you sure you want to delete this ingredient?') }}</strong>
    </template>
    <template #footer>
      <SecondaryButton @click="close()">
        {{ __('Nevermind') }}
      </SecondaryButton>
      <DangerButton
        class="ml-2"
        @click="confirmDelete()"
      >
        {{ __('Delete Ingredient') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>
