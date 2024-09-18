<script setup>
import {ref, onMounted} from 'vue';
import Loading from '@/Components/Loading.vue';
import {__} from '@/Composables/Translate';
import axios from 'axios';

const props = defineProps({
  teamId: {
    type: Number,
    default: null
  }
});

const reports = ref([]);
const loading = ref(true);

onMounted(() => {
  let url = '/reports/generated';

    if (props.teamId) {
        url = `${url}/${props.teamId}`;
    }

    axios.get(url).then(response => {
        reports.value = response.data;
        loading.value = false;
    });
  });
</script>

<template>
  <div class="bg-white overflow-hidden shadow rounded-b-lg divide-y divide-gray-200">
    <div class="px-4 py-5 sm:px-6">
      <h3 class="text-xl leading-6 font-medium text-gray-800">
        {{ __('Generated Reports') }}
      </h3>
    </div>
    <div class="px-4 py-5 sm:p-6">
      <p>
        {{ __('Below is a list of all generated reports. Each report will exist here for 14 days since its creation.') }}
      </p>
      <Loading
        v-if="loading"
        class="mt-6"
      />
      <ul
        v-else
        class="mt-6 divide-y divide-gray-200"
      >
        <li
          v-for="report in reports"
          :key="report.titleSlug"
          class="py-2"
        >
          <a
            :href="report.url"
            target="_blank"
            class="py-1 text-base leading-5 text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 transition ease-in-out duration-150 truncate"
          >
            {{ report.title }}
          </a>
        </li>
      </ul>
    </div>
  </div>
</template>
