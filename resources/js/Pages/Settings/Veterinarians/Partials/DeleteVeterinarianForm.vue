<template>
  <div>
    <FormSection>
      <template #title>
        {{ __('Delete :veterinarian', {veterinarian: veterinarian.name}) }}
      </template>

      <Alert
        class="col-span-4"
        color="red"
      >
        {{ __('Once a veterinarian is deleted, all of their and data will be permanently deleted. Before deleting this user, please download any data or information regarding this user that you wish to retain.') }}
      </Alert>

      <template #actions>
        <DangerButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="confirmingVeterinarianDeletion = true"
        >
          {{ __('Delete Veterinarian') }}
        </DangerButton>
      </template>
    </FormSection>

    <ConfirmationModal
      :show="confirmingVeterinarianDeletion"
      @close="confirmingVeterinarianDeletion = false"
    >
      <template #title>
        {{ __('Delete Veterinarian') }}
      </template>

      <template #content>
        <strong>{{ __('Are you sure you want to delete :veterinarian', {veterinarian: veterinarian.name}) }}?</strong> {{ __('Once a veterinarian is deleted, all of their resources and data will be permanently deleted.') }}
      </template>

      <template #footer>
        <SecondaryButton
          class="mr-3"
          @click="confirmingVeterinarianDeletion = false"
        >
          {{ __('Cancel') }}
        </SecondaryButton>

        <DangerButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="deleteVeterinarian"
        >
          {{ __('Delete :veterinarian', {veterinarian: veterinarian.name}) }}
        </DangerButton>
      </template>
    </ConfirmationModal>
  </div>
</template>

<script setup>
import { inject, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Alert from '@/Components/Alert.vue';

const route = inject('route');

const props = defineProps({
  veterinarian: Object
});

const confirmingVeterinarianDeletion = ref(false);
const form = useForm({});

const deleteVeterinarian = () => {
  form.delete(route('veterinarians.destroy', props.veterinarian.id));
};
</script>
