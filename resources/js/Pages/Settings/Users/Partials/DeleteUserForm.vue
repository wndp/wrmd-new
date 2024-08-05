<template>
  <div>
    <FormSection>
      <template #title>
        {{ __('Delete :user', {user: user.name}) }}
      </template>

      <Alert
        class="col-span-4"
        color="red"
      >
        {{ __('Once a user is deleted, all of their activity and data will be permanently deleted. Before deleting this user, please download any data or information regarding this user that you wish to retain.') }}
      </Alert>

      <template #actions>
        <DangerButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="confirmingUserDeletion = true"
        >
          {{ __('Delete User') }}
        </DangerButton>
      </template>
    </FormSection>

    <!-- Delete User Confirmation Modal -->
    <ConfirmationModal
      :show="confirmingUserDeletion"
      @close="confirmingUserDeletion = false"
    >
      <template #title>
        {{ __('Delete User') }}
      </template>

      <template #content>
        <strong>{{ __('Are you sure you want to delete :user', {user: user.name}) }}?</strong> {{ __('Once a user is deleted, all of their resources and data will be permanently deleted.') }}
      </template>

      <template #footer>
        <SecondaryButton
          class="mr-3"
          @click="confirmingUserDeletion = false"
        >
          {{ __('Nevermind') }}
        </SecondaryButton>

        <DangerButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="deleteUser"
        >
          {{ __('Delete :user', {user: user.name}) }}
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

const props =defineProps({
  user: Object
});

const confirmingUserDeletion = ref(false);
const form = useForm({});

const deleteUser = () => {
  form.delete(route('users.destroy', props.user.id));
};
</script>
