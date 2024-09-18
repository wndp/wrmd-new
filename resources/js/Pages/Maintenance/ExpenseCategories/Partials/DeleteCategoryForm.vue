<template>
  <div>
    <Panel>
      <template #heading>
        {{ __('Delete Category') }}
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
          {{ __('Delete Expense Category') }}
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
        <strong>{{ __('Are you sure you want to delete this category?') }}</strong>
        <p class="my-8 text-sm text-gray-700">
          {{ category.defaults_for_humans }}
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
          {{ __('Delete Category') }}
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
      category: {
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
            this.form.delete(this.route('maintenance.expense_categories.destroy', this.category));
        }
    },
};
</script>
