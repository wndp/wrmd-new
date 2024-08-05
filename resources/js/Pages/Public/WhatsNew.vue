<template>
  <PublicLayout title="What's New">
    <div class="relative py-8 px-4 sm:px-6 lg:px-8 bg-gray-50">
      <div class="absolute inset-0">
        <div class="h-96 bg-white" />
      </div>
      <div class="relative prose prose-lg mx-auto">
        <h1>
          <span class="mt-2 block text-3xl text-center leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">What's New</span>
        </h1>
        <div class="mt-6 text-xl text-gray-500 leading-8 text-center">
          <p>Stay up-to-date with Wildlife Rehabilitation MD news, updates and our plans for the future!</p>
        </div>
      </div>
      <div class="relative mt-12 grid gap-16 pt-12 lg:grid-cols-3 lg:gap-x-5 lg:gap-y-12">
        <div
          v-for="post in posts"
          :key="post.id"
          class="flex flex-col overflow-hidden rounded-lg shadow-lg"
        >
          <a
            :href="post.url"
            target="_blank"
          >
            <div class="flex-shrink-0">
              <img
                class="h-48 w-full object-cover"
                :src="post.featured_image"
                :alt="post.title"
              >
            </div>
          </a>
          <div class="flex flex-1 flex-col justify-between bg-white p-6">
            <div class="flex flex-wrap gap-2">
              <Badge
                v-for="tag in post.tags"
                :key="tag"
                color="yellow"
              >
                {{ tag }}
              </Badge>
            </div>
            <a
              :href="post.url"
              target="_blank"
              class="block flex-1 mt-4"
            >
              <p class="text-xl font-semibold text-gray-900">
                {{ post.title }}
              </p>
              <p class="mt-3 text-base text-gray-500">
                {{ post.excerpt }}&hellip;
              </p>
            </a>
            <div class="mt-6 flex items-center">
              <div>
                <div class="flex space-x-1 text-sm text-gray-500">
                  <time :datetime="post.published_at">
                    {{ post.published_at_for_humans }}
                  </time>
                </div>
                <p class="text-sm text-gray-500">
                  Author: <span class="font-bold">{{ post.author }}</span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Badge from '@/Components/Badge.vue';
import { parseISO, intlFormat } from 'date-fns';

defineProps({
  posts: Array
});

// export default {

//     props: {
//         posts: Array
//     },
//     methods: {
//         objectToColor(obj) {
//             return 'green';
//             if (obj.color) {
//                 return obj.color;
//             }

//             //obj.id
//         },
//         formatDate(date) {
//             return intlFormat(parseISO(date));
//         }
//     }
// };
</script>
