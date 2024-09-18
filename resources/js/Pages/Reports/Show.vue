<script setup>
import {ref, onMounted, nextTick} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ReportFilters from './Partials/ReportFilters.vue';
import ReportPreview from './Partials/ReportPreview.vue';
import ReportActions from './Partials/ReportActions.vue';
import { ArrowLongLeftIcon, InformationCircleIcon } from '@heroicons/vue/24/outline';
import {__} from '@/Composables/Translate';

const props = defineProps({
  report: {
    type: Object,
    required: true
  }
});

const baseUrl = `/reports/generate/${props.report.key}/?`;

const previewBody = ref(null);
const filters64 = ref('');
const expanded = ref(false);
const url = ref(baseUrl);


onMounted(() => {
  if (props.report.filters.length !== 0) {
    applyFilters([]);
  }

  nextTick(() => {
      let main = document.getElementById('main');
      let top = previewBody.value.getBoundingClientRect().top - 65 - 10;
      main.scrollTo({top: top, behavior: 'smooth'});
  });
});

const applyFilters = (filters) => {
    // base64 encoded filters
    filters64.value = window.btoa(JSON.stringify(filters));
    url.value = `${baseUrl}&filters=${filters64.value}`;
};
</script>

<template>
  <app-layout title="Reports">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ report.title }}
      </h1>
    </template>
    <Link
      :href="route('reports.index')"
      class="inline-flex items-center text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
    >
      <ArrowLongLeftIcon class="h-6 w-6 mr-2" />
      {{ __('Return to Reports List') }}
    </Link>
    <ReportFilters
      v-if="report.filters.length !== 0"
      :filters="report.filters"
      class="mt-4"
      @apply="applyFilters"
    />
    <blockquote
      v-if="report.explanation"
      class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-4"
    >
      <div class="flex">
        <div class="flex-shrink-0">
          <InformationCircleIcon
            class="h-5 w-5 text-yellow-400"
            aria-hidden="true"
          />
        </div>
        <div class="ml-3">
          <p class="text-base text-yellow-700">
            {{ report.explanation }}
          </p>
        </div>
      </div>
    </blockquote>
    <div
      class="shadow rounded-lg mt-4"
      :class="{'fixed z-30 w-screen h-screen top-0 left-0 mt-0 overflow-y-auto bg-gray-800 bg-opacity-60 rounded-none': expanded}"
    >
      <div
        id="previewBody"
        ref="previewBody"
        class="bg-white overflow-hidden shadow rounded-b-lg h-full flex flex-col"
      >
        <div class="px-4 py-5 sm:px-6">
          <ReportActions
            :url="url"
            :filters64="filters64"
            :report="report"
            @fullScreen="expanded = !expanded"
          />
        </div>
        <div class="px-4 pb-4 sm:px-6 flex-grow">
          <ReportPreview
            v-if="report.filters.length === 0 || filters64"
            :key="filters64"
            :url="url"
            :class="[expanded ? 'h-full' : 'h-screen']"
          />
        </div>
      </div>
    </div>
  </app-layout>
</template>
