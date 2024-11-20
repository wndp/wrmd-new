<template>
  <div>
    <FormSection>
      <template #title>
        Delete Testimonial from {{ testimonial.name }}
      </template>
      <Alert
        class="col-span-4"
        color="red"
      >
        Once you delete a testimonial, there is no going back. Please be certain. This action CANNOT be undone. This will permanently delete the testimonial.
      </Alert>
      <template #actions>
        <DangerButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="confirmingTestimonialDeletion = true"
        >
          Delete Testimonial
        </DangerButton>
      </template>
    </FormSection>
    <ConfirmationModal
      :show="confirmingTestimonialDeletion"
      @close="confirmingTestimonialDeletion = false"
    >
      <template #title>
        Delete Testimonial
      </template>

      <template #content>
        <strong>Are you sure you want to delete this testimonial?</strong>
      </template>

      <template #footer>
        <SecondaryButton
          class="mr-3"
          @click="confirmingTestimonialDeletion = false"
        >
          Cancel
        </SecondaryButton>

        <DangerButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="deleteTestimonial"
        >
          Yes
        </DangerButton>
      </template>
    </ConfirmationModal>
  </div>
</template>

<script>
import FormSection from '@/Components/FormElements/FormSection.vue';
import DangerButton from '@/Components/FormElements/DangerButton.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Alert from '@/Components/Alert.vue';

export default {
    components: {
        Alert,
        FormSection,
        DangerButton,
        SecondaryButton,
        ConfirmationModal
    },
    props: {
        testimonial: Object
    },
    data() {
        return {
            form: this.$inertia.form({}),
            confirmingTestimonialDeletion: false
        };
    },
    methods: {
        deleteTestimonial() {
            this.form.delete(this.route('admin.testimonials.destroy', this.testimonial.id));
        }
    },
};
</script>
