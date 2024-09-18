<script setup>
import {useForm} from '@inertiajs/vue3'
import AnalyticSegment from './AnalyticSegment.vue';
import AnalyticSegmentOptions from './AnalyticSegmentOptions.vue';
import {Popover, PopoverButton, PopoverPanel} from "@headlessui/vue";
import {Float} from '@headlessui-float/vue'
import {PlusIcon} from '@heroicons/vue/24/solid';
import {__} from '@/Composables/Translate';

const props = defineProps({
  segments: {
    type: Array,
    required: true
  }
});

const form = useForm({
  segments: [... props.segments]
});

const addSegment = (value, close) => {
  close();
  form.segments.push(value);
  saveSegments();
};

const updateSegment = (value, index) => {
  form.segments.splice(index, 1, value);
  saveSegments();
};

const removeSegment = (index) => {
  form.segments.splice(index, 1);
  saveSegments();
};

const saveSegments = () => {
  form.put('/analytics/filters', {
      preserveState: false,
  });
};
</script>

<template>
  <div class="flex flex-wrap gap-4 md:mr-8">
    <AnalyticSegment
      v-for="(segment, index) in segments"
      :key="index"
      :index="index"
      :segment="segment"
      :canRemove="segments.length > 1"
      @update-segment="updateSegment"
      @remove-segment="removeSegment"
    />
    <Popover
      v-if="segments.length < 4"
      as="div"
      class="relative inline-block text-left"
    >
      <Float
        placement="bottom"
        strategy="fixed"
        :offset="4"
        :flip="{crossAxis: true}"
      >
        <PopoverButton
          ref="trigger"
          class="inline-flex justify-center w-full px-4 py-4 text-sm font-medium text-black-500 bg-white border-2 border-dashed border-gray-300 hover:border-gray-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75 whitespace-nowrap"
        >
          {{ __('Add Segment') }}
          <PlusIcon
            class="w-5 h-5 ml-2"
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
          <div class="overflow-y-auto h-[32rem] w-96 flex-auto rounded-lg bg-white p-4 text-sm leading-6 shadow-lg ring-1 ring-gray-900/5">
            <AnalyticSegmentOptions
              @use-segment="addSegment($event, close)"
            />
          </div>
        </PopoverPanel>
      </Float>
    </Popover>
  </div>
</template>
