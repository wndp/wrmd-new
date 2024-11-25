<script setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import Panel from '@/Components/Panel.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Alert from '@/Components/Alert.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
  formula: {
    type: Object,
    required: true
  },
});

const form = useForm({});
const confirmingDeletion = ref(false);

const deleteRecipe = () => form.delete(route('maintenance.cookbook.destroy', props.formula));
</script>

<template>
  <div>
    <Panel>
      <template #title>
        {{ __('Delete Recipe') }}
      </template>
      <template #content>
        <Alert
          class="col-span-6"
          color="red"
        >
          {{ __('Deleting a recipe is safe. None of the nutrition plans used by this recipe will be deleted.') }}
        </Alert>
      </template>
      <template #actions>
        <DangerButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="confirmingDeletion = true"
        >
          {{ __('Delete Recipe') }}
        </DangerButton>
      </template>
    </Panel>

    <ConfirmationModal
      :show="confirmingDeletion"
      @close="confirmingDeletion = false"
    >
      <template #title>
        {{ __('Delete Recipe') }}
      </template>

      <template #content>
        <strong>{{ __('Are you sure you want to delete this recipe?') }}</strong>
        <p class="my-8 text-sm text-gray-700">
          {{ formula.defaults_for_humans }}
        </p>
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
          @click="deleteRecipe"
        >
          {{ __('Delete Recipe') }}
        </DangerButton>
      </template>
    </ConfirmationModal>
  </div>
</template>
