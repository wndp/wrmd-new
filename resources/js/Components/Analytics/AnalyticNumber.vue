<script setup>
import {ref, computed, onMounted} from 'vue';
import Loading from '@/Components/Loading.vue';
import {
    ArrowPathIcon,
    ArrowUpIcon,
    ArrowDownIcon,
    MinusIcon
} from '@heroicons/vue/24/outline';
import axios from 'axios';
import {__} from '@/Composables/Translate';

const props = defineProps({
    id: String,
    title: String,
    height: {
        type: Number,
        default: 100
    },
    urlParams: {
        type: Object,
        default () {
            return {};
        },
    }
});

const loading = ref(true);

const chart = ref({
    now: null,
    prev: null,
    change: null,
    difference: null
});

const color = computed(() => {
    switch(chart.value.change) {
        case 'up':
            return 'bg-green-200 text-green-800';
        case 'down':
            return 'bg-red-200 text-red-800';
        default:
            return 'bg-yellow-200 text-yellow-800';
    }
});

const getData = () => {
    axios.get('/analytics/numbers/' + props.id, {params: props.urlParams})
        .then(response => {
            chart.value.now = response.data.now;
            chart.value.prev = response.data.prev;
            chart.value.change = response.data.change;
            chart.value.difference = response.data.difference;
            loading.value = false;
        });
};

onMounted(() => getData());
</script>

<template>
  <div class="bg-white shadow overflow-hidden border-b border-gray-300 sm:rounded-lg relative">
    <Loading
      v-if="loading"
      :style="{height: `${height}px`}"
      style="min-height: 100px"
    />
    <div
      v-else
      class="px-4 py-3 sm:p-4"
      :style="{height: `${height}px`}"
      style="min-height: 100px"
    >
      <dl>
        <dt class="text-sm leading-6 font-normal text-gray-900">
          {{ title }}
        </dt>
        <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
          <div class="flex flex-wrap items-baseline text-2xl leading-8 font-semibold text-blue-600">
            <span class="mr-2">{{ chart.now }}</span>
            <span
              v-if="chart.prev"
              class="text-sm leading-5 font-medium text-gray-500"
            >
              {{ __('from') }} {{ chart.prev }}
            </span>
          </div>
          <div
            v-if="chart.prev"
            class="inline-flex items-baseline px-2.5 py-0.5 rounded-full text-sm font-medium leading-5 md:mt-2 lg:mt-0"
            :class="color"
          >
            <ArrowUpIcon
              v-if="chart.change === 'up'"
              class="-ml-1 mr-0.5 flex-shrink-0 self-center h-5 w-5 text-green-500"
            />
            <ArrowDownIcon
              v-else-if="chart.change === 'down'"
              class="-ml-1 mr-0.5 flex-shrink-0 self-center h-5 w-5 text-red-500"
            />
            <MinusIcon
              v-else
              class="-ml-1 mr-0.5 flex-shrink-0 self-center h-5 w-5 text-orange-500"
            />
            {{ chart.difference }}%
          </div>
        </dd>
      </dl>
    </div>
  </div>
</template>
