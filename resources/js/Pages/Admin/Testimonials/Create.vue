<script setup>
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import TestimonialForm from './Partials/TestimonialForm.vue';
import AdminNavigation from '../Partials/AdminNavigation.vue';
import {__} from '@/Composables/Translate';

defineProps({
    teams: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: '',
    text: '',
    team_id: '',
});

const storeTestimonial = () => {
    form.post(route('admin.testimonials.store'), {
        preserveScroll: true
    });
};
</script>

<template>
  <AppLayout title="Testimonials">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <AdminNavigation class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <TestimonialForm
          :form="form"
          :title="__('New Testimonial')"
          :action="__('Create New Testimonial')"
          :teams="teams"
          @saved="storeTestimonial"
        />
      </div>
    </div>
  </AppLayout>
</template>
