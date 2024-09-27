<script setup>
import { inject, ref, reactive, watch } from 'vue';
import MediaDetails from './MediaDetails.vue';
import Draggable from 'vuedraggable';

const route = inject('route');

const props = defineProps({
  media: {
    type: Array,
    required: true
  },
  resource: {
    type: String,
    required: true
  },
  resourceId: {
    type: String,
    required: true
  }
});

const componentKey = ref(0);
let showMediaDetails = ref(false);
let mediaToDetail = reactive({});
const mediaList = ref(props.media);

watch(() => props.media, media => mediaList.value = media)

let showDetailsFor = (media) => {
  componentKey.value += 1;
  mediaToDetail = media;
  showMediaDetails.value = true;
};

let detailNext = () => {
  let idx = props.media.findIndex(media => media.id === mediaToDetail.id);
  let nextIdx = idx === props.media.length-1 ? idx : idx+1;
  showDetailsFor(props.media[nextIdx]);
}

let detailPrevious = () => {
  let idx = props.media.findIndex(media => media.id === mediaToDetail.id);
  let nextIdx = idx === 0 ? idx : idx-1;
  showDetailsFor(props.media[nextIdx]);
}

const onChange = () => {
  let mediaIds = mediaList.value.map(media => media.id);
  window.axios.post(route('media.order'), {
    media: mediaIds
  })
}
</script>

<template>
  <div>
    <Draggable
      v-model="mediaList"
      role="list"
      class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 sm:gap-x-6 lg:grid-cols-4 xl:gap-x-8"
      element="ul"
      itemKey="name"
      @change="onChange"
    >
      <template #item="{ element }">
        <div class="relative">
          <div :class="[element.current ? 'ring-2 ring-offset-2 ring-green-500' : 'hover:ring-2 hover:ring-offset-2 hover:ring-offset-gray-100 hover:ring-green-500', 'group block w-full aspect-w-10 aspect-h-7 rounded-lg bg-gray-100 overflow-hidden']">
            <img
              :src="element.preview_url"
              :alt="element.name"
              :class="[element.current ? '' : 'group-hover:opacity-75', 'object-cover pointer-events-none']"
            >
            <button
              type="button"
              class="absolute inset-0 focus:outline-none"
              @click="showDetailsFor(element)"
            >
              <span class="sr-only">View details for {{ element.name }}</span>
            </button>
          </div>
          <p class="mt-2 block text-sm font-medium text-gray-900 truncate pointer-events-none">
            {{ element.name }}
          </p>
          <span class="text-sm font-medium text-gray-500 pointer-events-none">
            {{ element.custom_properties.obtained_at_formatted }}
          </span>
        </div>
      </template>
    </Draggable>
    <MediaDetails
      v-if="showMediaDetails"
      :key="componentKey"
      :show="true"
      :media="mediaToDetail"
      :resource="resource"
      :resourceId="resourceId"
      @close="showMediaDetails = false"
      @next="detailNext"
      @previous="detailPrevious"
    />
  </div>
</template>
