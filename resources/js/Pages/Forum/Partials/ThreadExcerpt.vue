<script setup>
import { ChatBubbleOvalLeftEllipsisIcon } from '@heroicons/vue/24/outline';

defineProps({
  thread: {
    type: Object,
    required: true
  },
  showExcerpt: Boolean
});
</script>

<template>
  <Link
    :href="route('forum.show', thread)"
    class="relative rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm flex space-x-3 hover:border-gray-300 hover:ring-2 hover:ring-offset-2 hover:ring-blue-500 cursor-pointer"
  >
    <div class="flex-shrink-0">
      <img
        class="h-16 w-16 rounded-md"
        :src="thread.team.profile_photo_url"
        :alt="thread.team.organization"
      >
    </div>
    <div class="flex-1 min-w-0">
      <div class="h-full flex flex-col justify-between">
        <div class="md:flex md:items-start -mt-1">
          <h4 class="mb-4 lg:mb-0 md:pr-6 self-center truncate">
            {{ thread.title }}
          </h4>
          <div class="hidden md:flex items-center text-center md:ml-auto relative">
            <div class="flex items-center justify-center text-gray-500">
              <ChatBubbleOvalLeftEllipsisIcon class="mr-1 h-5 w-5" />
              <span class="text-sm font-medium text-left leading-none relative">
                {{ thread.replies_count }}
              </span>
            </div>
            <span class="ml-4 text-sm text-gray-500 whitespace-nowrap">{{ thread.channel.name }}</span>
          </div>
        </div>
        <div
          v-if="showExcerpt"
          class="my-3 lg:pr-8 line-clamp-2 break-words text-gray-700 text-base md:text-sm leading-normal"
        >
          {{ thread.body }}
        </div>
        <div class="text-gray-500 text-xs leading-none tracking-tight">
          {{ thread.user?.name }} at <b>{{ thread.team.name }}</b> posted <time class="font-medium">{{ thread.created_at_for_humans }}</time>
        </div>
      </div>
    </div>
  </Link>
</template>
