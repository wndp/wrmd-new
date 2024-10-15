<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import FormSection from '@/Components/FormElements/FormSection.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Alert from '@/Components/Alert.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  person: Object
});

const form = useForm({});

const confirmingDeletion = ref(false);

const deletePerson = () => useForm().delete(route('people.destroy', props.person.id));

//const goBack = () => router().get(route('people.rescuers.index'));
</script>

<template>
  <div>
    <FormSection>
      <template #title>
        {{ __('Delete :person', {person: person.identifier}) }}
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
          @click="confirmingDeletion = true"
        >
          {{ __('Delete Person') }}
        </DangerButton>
      </template>
    </FormSection>

    <ConfirmationModal
      :show="confirmingDeletion"
      @close="confirmingDeletion = false"
    >
      <template #title>
        {{ __('Delete Person') }}
      </template>

      <template #content>
        <strong>{{ __('Are you sure you want to delete :person', {person: person.identifier}) }}?</strong>
      </template>

      <template #footer>
        <SecondaryButton
          class="mr-3"
          @click="confirmingDeletion = false"
        >
          {{ __('Cancel') }}
        </SecondaryButton>

        <DangerButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="deletePerson"
        >
          {{ __('Delete :person', {person: person.identifier}) }}
        </DangerButton>
      </template>
    </ConfirmationModal>
  </div>
</template>
