<template>
  <div>
    <Panel>
      <template #heading>
        {{ __('Delete Formula') }}
      </template>

      <Alert
        class="col-span-4"
        color="red"
      >
        {{ __('Deleting a formula is safe. None of the prescriptions used by this formula will be deleted.') }}
      </Alert>

      <template #footing>
        <DangerButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="confirmingDeletion = true"
        >
          {{ __('Delete Prescription Formula') }}
        </DangerButton>
      </template>
    </Panel>

    <ConfirmationModal
      :show="confirmingDeletion"
      @close="confirmingDeletion = false"
    >
      <template #title>
        {{ __('Delete Formula') }}
      </template>

      <template #content>
        <strong>{{ __('Are you sure you want to delete this formula?') }}</strong>
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
          @click="deleteFormula"
        >
          {{ __('Delete Formula') }}
        </DangerButton>
      </template>
    </ConfirmationModal>
  </div>
</template>

<script>
import Panel from '@/Components/Panel.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Alert from '@/Components/Alert.vue';

export default {
    components: {
        Panel,
        DangerButton,
        SecondaryButton,
        Alert,
        ConfirmationModal
    },
    props: {
      formula: {
        type: Object,
        default: () => ({})
      },
    },
    data() {
        return {
            form: this.$inertia.form({}),
            confirmingDeletion: false
        };
    },
    methods: {
        deleteFormula() {
            this.form.delete(this.route('maintenance.formulas.destroy', this.formula));
        }
    },
};
</script>
