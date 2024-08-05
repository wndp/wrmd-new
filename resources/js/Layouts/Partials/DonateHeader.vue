<script setup>
import {computed} from 'vue';
import {HeartIcon} from '@heroicons/vue/24/outline';

const year = computed(() => new Date().getFullYear() + 1);
</script>

<template>
  <div class="relative bg-white">
    <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
      <div class="pr-12 sm:px-12 flex">
        <div class="mr-4 flex-shrink-0">
          <span class="inline-flex items-center justify-center p-2 bg-red-300 rounded-md shadow">
            <HeartIcon
              class="h-6 w-6 sm:h-8 sm:w-8 text-red-500 animate-pulse"
              aria-hidden="true"
            />
          </span>
        </div>
        <div>
          <p class="font-medium text-sm">
            <span class="md:hidden text-gray-500 mr-2">
              {{ __('Fewer than 5% of our users have donated, but if everyone seeing this gave just $25 we would be set for :year.', {year}) }} <span class="font-bold whitespace-nowrap">{{ __('Can You Help?') }}</span>
            </span>
            <span class="hidden md:inline text-gray-500 mr-8">
              {{ __("Wildlife Rehabilitation MD (WRMD) is a not-for-profit program serving over 1,350 wildlife rehabilitation organizations across 27 countries. It's free to use but not free for us to maintain.") }} <span class="bg-yellow-100 px-1">{{ __('Fewer than 5% of our users have donated, but if everyone seeing this gave just $25 we would be set for :year.', {year}) }}</span> <span class="font-bold whitespace-nowrap">{{ __('Can You Help?') }}</span>
            </span>
            <span class="block mt-1 sm:inline-block text-red-500">
              <Link
                :href="route('donate.index', {'defaultInterval': 'm'})"
                class="text-sm font-bold underline mr-8"
              >{{ __('Give Monthly') }} <span aria-hidden="true">&rarr;</span></Link>
              <Link
                :href="route('donate.index', {'defaultInterval': 'o'})"
                class="text-sm font-bold underline"
              >{{ __('Give Now') }} <span aria-hidden="true">&rarr;</span></Link>
            </span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
