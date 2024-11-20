<template>
  <AppLayout title="Testimonials">
    <TestimonialForm
      v-model="form.testimonial"
      :title="__('Update Testimonial')"
      :action="__('Update Testimonial')"
      :can-submit="false"
      :testimonial="testimonial"
      :accounts="accounts"
      :errors="form.errors"
      @saved="storeTestimonial"
    />
    <DeleteTestimonialForm
      :testimonial="testimonial"
      class="mt-8"
    />
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TestimonialForm from './Partials/TestimonialForm.vue';
import DeleteTestimonialForm from './Partials/DeleteTestimonialForm.vue';

defineProps({
    testimonial: {
        type: Object,
        required: true
    },
    accounts: {
        type: Object,
        required: true
    }
})
</script>

<script>
export default {
    data() {
        return {
            form: this.$inertia.form({
                testimonial: {}
            })
        }
    },
    methods: {
        storeTestimonial() {
            this.form.transform((data) => ({
              ...data.testimonial,
            })).put(this.route('admin.testimonials.update', this.testimonial), {
                preserveScroll: true
            });
        }
    },
};
</script>
