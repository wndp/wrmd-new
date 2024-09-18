<template>
  <Popover
    v-slot="{ open }"
    as="div"
    class="relative"
  >
    <Float
      strategy="fixed"
      :offset="4"
      :flip="{crossAxis: true}"
    >
      <PopoverButton
        ref="trigger"
        :class="open ? '' : 'text-opacity-90'"
        class="inline-flex justify-center w-full px-4 py-4 text-sm font-medium text-black-500 bg-white border-2 border-dashed border-gray-300 hover:border-gray-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75 whitespace-nowrap"
      >
        {{ segment }}
        <ChevronDownIcon
          :class="open ? '' : 'text-opacity-70'"
          class="ml-2 h-5 w-5 transition duration-150 ease-in-out group-hover:text-opacity-80"
          aria-hidden="true"
        />
      </PopoverButton>
      <PopoverPanel
        ref="container"
        v-slot="{ close }"
        :unmount="false"
        class="shadow-lg"
        style="z-index: 1010"
      >
        <div
          class="overflow-y-auto h-[32rem] flex-auto rounded-lg bg-white p-4 text-sm leading-6 ring-1 ring-gray-900/5"
        >
          <button
            v-if="canRemove"
            type="button"
            class="inline-flex w-full py-3 text-sm font-medium text-red-400 hover:text-red-600"
            @click="removeSegment"
          >
            <TrashIcon
              class="w-5 h-5 mr-2"
              aria-hidden="true"
            />
            Remove Segment
          </button>
          <AnalyticSegmentOptions
            @use-segment="updateSegment($event, close)"
          />
        </div>
      </PopoverPanel>
    </Float>
  </Popover>
</template>

<script setup>
import AnalyticSegmentOptions from './AnalyticSegmentOptions.vue';
import { Popover, PopoverButton, PopoverPanel } from "@headlessui/vue";
import { ChevronDownIcon, TrashIcon } from '@heroicons/vue/24/solid';
import { Float } from '@headlessui-float/vue'

const props = defineProps({
  index: {
    type: Number,
    required: true
  },
  segment: {
    type: String,
    required: true
  },
  canRemove: {
      type: Boolean,
      default: false
  }
});

const emit = defineEmits(['update-segment', 'remove-segment']);

const updateSegment = (value, close) => {
  emit('update-segment', value, props.index);
  close();
};

const removeSegment = () => {
  emit('remove-segment', props.index);
}
</script>
