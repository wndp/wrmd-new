<template>
  <AppLayout title="Testimonials">
    <TestimonialForm
      v-model="form.testimonial"
      :title="__('New Testimonial')"
      :action="__('Create New Testimonial')"
      :can-submit="false"
      :accounts="accounts"
      :errors="form.errors"
      @saved="storeTestimonial"
    />
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TestimonialForm from './Partials/TestimonialForm.vue';

defineProps({
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
            })).post(this.route('admin.testimonials.store'), {
                preserveScroll: true
            });
        }
    },
};
</script>
