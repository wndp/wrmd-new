<script setup>
import { ref, computed, watchEffect, onMounted } from 'vue';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid';
import {__} from '@/Composables/Translate';

const props = defineProps({
  properties: {
    type: Object,
    required: true
  }
});

const propertiesProxy = ref(props.properties);
const totalRows = computed(() => new Intl.NumberFormat().format(props.properties.total));

watchEffect(() => props.properties, () => popDots());
onMounted(() => popDots());

const popDots = () => {
  propertiesProxy.value.links.pop();
  propertiesProxy.value.links.shift();
}
</script>

<template>
  <div
    v-if="propertiesProxy.total > 0"
    class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
  >
    <div
      v-if="propertiesProxy.links.length > 1"
      class="flex-1 flex justify-between sm:hidden"
    >
      <span
        v-if="propertiesProxy.prev_page_url === null"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white"
      >
        <ChevronLeftIcon
          class="h-5 w-5 mr-1"
          aria-hidden="true"
        />
        {{ __('Previous') }}
      </span>
      <Link
        v-else
        :href="propertiesProxy.prev_page_url"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
      >
        <ChevronLeftIcon
          class="h-5 w-5 mr-1"
          aria-hidden="true"
        />
        {{ __('Previous') }}
      </Link>

      <span
        v-if="propertiesProxy.next_page_url === null"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white"
      >
        {{ __('Next') }}
        <ChevronRightIcon
          class="h-5 w-5 ml-1"
          aria-hidden="true"
        />
      </span>
      <Link
        v-else
        :href="propertiesProxy.next_page_url"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
      >
        {{ __('Next') }}
        <ChevronRightIcon
          class="h-5 w-5 ml-1"
          aria-hidden="true"
        />
      </Link>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          {{ __('Showing') }}
          {{ ' ' }}
          <template v-if="propertiesProxy.total > 0">
            <span class="font-medium">{{ propertiesProxy.from }}</span>
            {{ ' ' }}
            {{ __('to') }}
            {{ ' ' }}
            <span class="font-medium">{{ propertiesProxy.to }}</span>
            {{ ' ' }}
            {{ __('of') }}
            {{ ' ' }}
          </template>
          <span class="font-medium">{{ totalRows }}</span>
          {{ ' ' }}
          {{ __('results') }}
        </p>
      </div>
      <div v-if="propertiesProxy.links.length > 1">
        <nav
          class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
          aria-label="Pagination"
        >
          <span
            v-if="propertiesProxy.prev_page_url === null"
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500"
          >
            <ChevronLeftIcon
              class="h-5 w-5"
              aria-hidden="true"
            />
          </span>
          <Link
            v-else
            :href="propertiesProxy.prev_page_url"
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
          >
            <span class="sr-only">{{ __('Previous') }}</span>
            <ChevronLeftIcon
              class="h-5 w-5"
              aria-hidden="true"
            />
          </Link>

          <template v-for="link in propertiesProxy.links">
            <span
              v-if="link.url === null"
              :key="link.label"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
            >
              ...
            </span>
            <Link
              v-else
              :key="link.label"
              :href="link.url"
              aria-current="page"
              preserve-scroll
              :class="[link.active ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50']"
              class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
            >
              {{ link.label }}
            </Link>
          </template>

          <span
            v-if="propertiesProxy.next_page_url === null"
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500"
          >
            <ChevronRightIcon
              class="h-5 w-5"
              aria-hidden="true"
            />
          </span>
          <Link
            v-else
            :href="propertiesProxy.next_page_url"
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
          >
            <span class="sr-only">{{ __('Next') }}</span>
            <ChevronRightIcon
              class="h-5 w-5"
              aria-hidden="true"
            />
          </Link>
        </nav>
      </div>
    </div>
  </div>
</template>
