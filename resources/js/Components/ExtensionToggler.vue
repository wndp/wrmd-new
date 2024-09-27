<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { BookmarkSquareIcon, InformationCircleIcon } from '@heroicons/vue/24/outline';
import * as heroIcons from "@heroicons/vue/24/outline";
import {__} from '@/Composables/Translate';

const route = inject('route');

const props = defineProps({
  extension: {
    type: Object,
    required: true
  },
  account: {
    type: Object,
    default: () => null
  }
});

const toggle = () => {
  let formRoute = props.extension.is_activated ? 'extensions.destroy' : 'extensions.store';
  useForm({}).submit(
    props.extension.is_activated ? 'delete' : 'post',
    route(formRoute, {
      extension: props.extension.value,
      account: props.account
    }),
    {
      preserveScroll: true
    }
  );
};

const showDetails = () => {
  window.Beacon('article', props.extension.knowledge_base_id, { type: 'sidebar' })
}
</script>

<template>
  <li
    :id="`extension-${extension.value}`"
    class="col-span-1 bg-white rounded-lg divide-y divide-gray-200 border-4"
    :class="[extension.is_activated ? 'border-green-300 shadow-md' : 'border-gray-100 shadow-inner']"
  >
    <div class="w-full h-4/6 flex items-center justify-between p-6 space-x-6">
      <div class="flex-1">
        <div class="flex flex-wrap items-center">
          <h3 class="text-gray-900 text-sm font-medium truncate mr-3">
            {{ extension.label }}
          </h3>
        </div>
        <p class="mt-1 text-gray-500 text-sm">
          {{ extension.description }}
        </p>
      </div>
      <component
        :is="heroIcons[extension.icon]"
        class="w-10 h-10 text-blue-600 flex-shrink-0"
        aria-hidden="true"
      />
    </div>
    <div class="h-2/6">
      <div class="-mt-px flex divide-x divide-gray-200">
        <div class="w-0 flex-1 flex">
          <button
            type="button"
            class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500"
            @click="toggle"
          >
            <BookmarkSquareIcon
              class="w-5 h-5 text-gray-400"
              aria-hidden="true"
            />
            <span
              class="ml-3"
              :class="[extension.is_activated ? 'text-red-600' : 'text-green-700']"
            >{{ extension.is_activated ? __('Deactivate') : __('Activate') }}</span>
          </button>
        </div>
        <div class="-ml-px w-0 flex-1 flex">
          <a
            href="javascript:void(0)"
            class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm text-gray-600 font-medium border border-transparent rounded-br-lg hover:text-gray-500"
            @click="showDetails"
          >
            <InformationCircleIcon
              class="w-5 h-5 text-gray-400"
              aria-hidden="true"
            />
            <span class="ml-3">{{ __('Details') }}</span>
          </a>
        </div>
      </div>
    </div>
  </li>
</template>
