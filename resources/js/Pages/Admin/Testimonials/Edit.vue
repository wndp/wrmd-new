<script setup>
import {useForm} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import TestimonialForm from './Partials/TestimonialForm.vue';
import DeleteTestimonialForm from './Partials/DeleteTestimonialForm.vue';
import AdminNavigation from '../Partials/AdminNavigation.vue';
import {__} from '@/Composables/Translate';

const props = defineProps({
    testimonial: {
        type: Object,
        required: true
    },
    teams: {
        type: Object,
        required: true
    }
});

const form = useForm({
  name: props.testimonial.name,
  text: props.testimonial.text,
  team_id: props.testimonial.team_id,
});

const storeTestimonial = () => {
    form.put(route('admin.testimonials.update', props.testimonial), {
        preserveScroll: true
    });
};
</script>

<script>
// export default {
//     data() {
//         return {
//             form: this.$inertia.form({
//                 testimonial: {}
//             })
//         }
//     },
//     methods: {
//         storeTestimonial() {
//             this.form.transform((data) => ({
//               ...data.testimonial,
//             })).put(this.route('admin.testimonials.update', this.testimonial), {
//                 preserveScroll: true
//             });
//         }
//     },
// };
</script>

<template>
  <AppLayout title="Testimonials">
    <div class="lg:grid grid-cols-8 gap-8 mt-4">
      <AdminNavigation class="mb-4 lg:mb-0 col-span-2" />
      <div class="col-span-6">
        <TestimonialForm
          :form="form"
          :title="__('Update Testimonial')"
          :action="__('Update Testimonial')"
          :testimonial="testimonial"
          :teams="teams"
          @saved="storeTestimonial"
        />
        <DeleteTestimonialForm
          :testimonial="testimonial"
          class="mt-8"
        />
      </div>
    </div>
  </AppLayout>
</template>
