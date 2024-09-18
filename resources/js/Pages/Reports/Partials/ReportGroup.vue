<script setup>
import ToggleFavoriteReport from './ToggleFavoriteReport.vue';

defineProps({
  title: String,
  reports: Array
});
</script>

<template>
  <div class="bg-white overflow-hidden shadow rounded-b-lg divide-y divide-gray-200">
    <div class="px-4 py-5 sm:px-6">
      <h3 class="text-xl leading-6 font-medium text-gray-800">
        {{ title }}
      </h3>
    </div>
    <div class="px-4 py-5 sm:p-6">
      <slot>
        <div class="divide-y divide-gray-200 sm:divide-none sm:grid sm:grid-cols-2 sm:gap-12 md:gap-24">
          <ul
            v-for="(reportGroupColumn, i) in reports"
            :key="i"
            class="divide-y divide-gray-200"
          >
            <li
              v-for="report in reportGroupColumn"
              :key="report.titleSlug"
              class="py-2"
            >
              <div class="flex align-items justify-between">
                <a
                  v-if="report.isAsset"
                  :href="report.url"
                  target="_blank"
                  download
                  class="text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
                >
                  {{ report.title }}
                </a>
                <Link
                  v-else
                  :href="report.url"
                  class="text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150"
                >
                  {{ report.title }}
                </Link>
                <ToggleFavoriteReport
                  v-if="report.canFavorite"
                  :report="report"
                />
              </div>
            </li>
          </ul>
        </div>
      </slot>
    </div>
  </div>
</template>
