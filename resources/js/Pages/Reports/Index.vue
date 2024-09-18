<script setup>
import {inject, ref, computed, onMounted, onUnmounted} from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ReportGroup from './Partials/ReportGroup.vue';
import FavoriteReports from './Partials/FavoriteReports.vue';
import GeneratedReports from './Partials/GeneratedReports.vue';
import { DocumentTextIcon, ClockIcon } from '@heroicons/vue/24/outline';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import {__} from '@/Composables/Translate';

const emitter = inject('emitter');

const props = defineProps({
  reportsCollection: {
    type: Array,
    required: true
  },
  favoriteReports: {
    type: Array,
    required: true
  },
});

const favorites = ref(props.favoriteReports);

const reports = computed(() => props.reportsCollection.filter(r => r.visibility));

const toggleFavorite = (report) => {
  if (report.isFavorited) {
    favorites.value.push(report);
  } else {
    favorites.value = favorites.value.filter(r => r.key !== report.key);
  }
};

onMounted(() => emitter.on('report.toggle-favorite', toggleFavorite));
onUnmounted(() => emitter.off('report.toggle-favorite', toggleFavorite));
</script>

<template>
  <AppLayout title="Reports">
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 mb-8">
        {{ __('Reports') }}
      </h1>
    </template>
    <TabGroup>
      <TabList class="border-b border-gray-200">
        <nav
          class="-mb-px flex space-x-4"
          aria-label="Tabs"
        >
          <Tab
            v-slot="{ selected }"
            as="template"
          >
            <button
              :class="[selected ? 'bg-white text-gray-700' : 'text-gray-500 hover:text-gray-700', 'flex items-center px-3 py-2 font-medium text-base rounded-t-md']"
              :aria-current="selected ? 'page' : undefined"
            >
              <DocumentTextIcon class="text-gray-500 mr-3 flex-shrink-0 h-5 w-5" />
              {{ __('Standard') }}
            </button>
          </Tab>
          <!-- <Tab
            v-slot="{ selected }"
            as="template"
          >
            <button
              :class="[selected ? 'bg-white text-gray-700' : 'text-gray-500 hover:text-gray-700', 'flex items-center px-3 py-2 font-medium text-base rounded-t-md']"
              :aria-current="selected ? 'page' : undefined"
            >
              <WrenchScrewdriverIcon class="text-gray-500 mr-3 flex-shrink-0 h-5 w-5" />
              {{ __('Custom Reports') }}
            </button>
          </Tab> -->
          <Tab
            v-slot="{ selected }"
            as="template"
          >
            <button
              :class="[selected ? 'bg-white text-gray-700' : 'text-gray-500 hover:text-gray-700', 'flex items-center px-3 py-2 font-medium text-base rounded-t-md']"
              :aria-current="selected ? 'page' : undefined"
            >
              <ClockIcon class="text-gray-500 mr-3 flex-shrink-0 h-5 w-5" />
              {{ __('Generated') }}
            </button>
          </Tab>
        </nav>
      </TabList>
      <TabPanels>
        <TabPanel class="space-y-6">
          <FavoriteReports :reports="favorites" />
          <ReportGroup
            v-for="reportGroup in reports"
            :id="`reports-${reportGroup.titleSlug}`"
            :key="reportGroup.title"
            :title="reportGroup.title"
            :reports="reportGroup.reports"
          />
        </TabPanel>
        <!-- <TabPanel>
          ...
        </TabPanel> -->
        <TabPanel>
          <GeneratedReports />
        </TabPanel>
      </TabPanels>
    </TabGroup>
  </AppLayout>
</template>
