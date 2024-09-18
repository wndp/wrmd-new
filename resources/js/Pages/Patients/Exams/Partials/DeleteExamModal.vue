<template>
  <ConfirmationModal
    :show="show"
    max-width="2xl"
    @close="close"
  >
    <template #title>
      {{ __('Delete Exam From Patient') }}
    </template>
    <template #content>
      <strong>{{ __('Are you sure you want to delete this exam?') }}</strong>
      <p class="text-sm mt-4">{{ exam.summary_body }}</p>
    </template>
    <template #footer>
      <SecondaryButton @click="close">
        {{ __('Nevermind') }}
      </SecondaryButton>
      <DangerButton
        class="ml-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="deleteExam"
      >
        {{ __('Delete Exam') }}
      </DangerButton>
    </template>
  </ConfirmationModal>
</template>

<script>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';

export default {
    components: {
        ConfirmationModal,
        DangerButton,
        SecondaryButton
    },
    props: {
        patient: {
            type: Object,
            required: true
        },
        exam: {
            type: Object,
            default: () => ({})
        },
        show: Boolean,
    },
    emits: ['close'],
    data() {
      return {
        form: this.$inertia.form({}),
      };
    },
    methods: {
        close() {
            this.$emit('close');
        },
        deleteExam() {
            this.form.delete(this.route('patients.exam.destroy', {
                patient: this.patient,
                exam: this.exam
            }), {
                preserveState: false,
                onSuccess: () => this.close()
            });
        },
    }
};
</script>
