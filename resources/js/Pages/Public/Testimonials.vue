<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
  testimonials: {
    type: Array,
    required: true
  }
})
</script>

<template>
  <PublicLayout title="Testimonials">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="prose prose-lg mx-auto">
        <h1>
          <span class="mt-2 block text-3xl text-center leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">We <span class="text-red-500">LOVE</span> Getting Testimonials!</span>
        </h1>
        <p class="mt-8 text-xl text-gray-500 leading-8">
          Thank you for sharing how Wildlife Rehabilitation MD has improved your day-to-day wildlife rehabilitation.
        </p>
      </div>
      <div class="mt-6 pros pros-xl pros-blue max-w-none text-gray-500 mx-auto">
        <div class="max-w-7xl mx-auto lg:px-8">
          <div
            v-for="testimonial in testimonials"
            :key="testimonial.id"
            class="py-8 px-4 sm:px-6 md:flex md:flex-col"
          >
            <blockquote class="mt-6 md:flex-grow md:flex md:flex-col pl-8 border-l-2 border-gray-300">
              <div class="relative text-lg font-normal leading-8 text-gray-500 md:flex-grow">
                <svg
                  class="absolute top-0 left-0 transform -translate-x-3 -translate-y-2 h-8 w-8 text-green-300"
                  fill="currentColor"
                  viewBox="0 0 32 32"
                  aria-hidden="true"
                >
                  <path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z" />
                </svg>
                <p class="relative">
                  {{ testimonial.text }}
                </p>
              </div>
              <footer class="mt-8">
                <div class="flex items-start">
                  <div class="flex-shrink-0 inline-flex rounded-full border-2 border-white">
                    <img
                      class="h-12 w-12 rounded-full"
                      :src="testimonial.team.profile_photo_url"
                      alt="Logo"
                    >
                  </div>
                  <div class="ml-4">
                    <div class="text-base font-medium text-gray-400">
                      {{ testimonial.name }}
                    </div>
                    <div class="text-base font-medium text-gray-600">
                      {{ testimonial.team.name }},
                      {{ testimonial.team.locale }}
                    </div>
                  </div>
                </div>
              </footer>
            </blockquote>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>
