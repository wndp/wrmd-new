<script setup>
import {ref, computed, useSlots} from 'vue';
import md5 from 'md5';
import { ExclamationCircleIcon, XMarkIcon } from '@heroicons/vue/24/solid';
import { useCookies } from "vue3-cookies";
import {__} from '@/Composables/Translate';

const props = defineProps({
  color: {
    type: String,
    default: 'yellow'
  },
  dismissible: {
    type: Boolean,
    default: false
  }
});

const { cookies } = useCookies();

const dismissed = ref(false)

const defaultSlotContent = useSlots().default();

const dismissibleCookieKey = computed(() => {
  let encodedeContent = md5(defaultSlotContent);
  return `alert-${encodedeContent}`;
});

const isDismissed = computed(() => {
  return props.dismissible && (dismissed.value || cookies.isKey(dismissibleCookieKey.value));
});

const classes = computed(() => {
  switch (props.color) {
      case 'red':
        return 'bg-red-100 text-red-800 border-red-200';
      case 'green':
          return 'bg-green-100 text-green-800 border-green-200';
      case 'blue':
          return 'bg-blue-100 text-blue-800 border-blue-200';
      case 'yellow':
      default:
          return 'bg-yellow-100 text-yellow-800 border-yellow-200';
  }
});

const dismissibleClasses = computed(() => {
  switch (props.color) {
    case 'red':
        return 'bg-red-100 text-red-500 hover:bg-red-50 focus:ring-offset-red-100 focus:ring-red-600';
    case 'green':
        return 'bg-green-100 text-green-500 hover:bg-green-50 focus:ring-offset-green-100 focus:ring-green-600';
    case 'blue':
        return 'bg-blue-100 text-blue-500 hover:bg-blue-50 focus:ring-offset-blue-100 focus:ring-blue-600';
    case 'yellow':
    default:
      return 'bg-yellow-100 text-yellow-500 hover:bg-yellow-50 focus:ring-offset-yellow-100 focus:ring-yellow-600';
  }
});

const dismiss = () => {
  dismissed.value = true;
  cookies.set(dismissibleCookieKey.value, true, Infinity);
}
</script>
<template>
  <div
    v-if="! isDismissed"
    class="rounded-md p-4 border"
    :class="classes"
  >
    <div class="flex">
      <div class="flex-shrink-0">
        <ExclamationCircleIcon
          class="h-5 w-5"
          aria-hidden="true"
        />
      </div>
      <div class="ml-3 text-sm font-medium">
        <slot />
      </div>
      <div
        v-if="dismissible"
        class="ml-auto pl-3"
      >
        <div class="-mx-1.5 -my-1.5">
          <button
            type="button"
            :class="dismissibleClasses"
            class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
            @click="dismiss"
          >
            <span class="sr-only">{{ __('Dismiss') }}</span>
            <XMarkIcon class="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
