<script setup>
import {ref, onMounted} from 'vue';
import Loading from '@/Components/Loading.vue';
import {__} from '@/Composables/Translate';
import axios from 'axios';

const props = defineProps({
  id: {
    type: String,
    required: true
  },
  urlParams: {
      type: Object,
      default () {
          return {};
      }
  },
});

const rows = ref([]);
const loading = ref(true);

const getData = () => {
  axios.get('/analytics/tables/' + props.id, {
    params: props.urlParams
  })
  .then(response => {
      rows.value = response.data.series;
      loading.value = false;
  });
};

onMounted(() => getData());
</script>

<template>
  <div>
    <div
      v-if="loading"
      class="py-8 shadow overflow-hidden border-b border-gray-200 sm:rounded-lg bg-white"
    >
      <Loading />
    </div>
    <div
      v-else
      class="flex flex-col"
    >
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table
              :id="`${id}-data-table`"
              class="min-w-full divide-y divide-gray-200 bg-white"
            >
              <thead class="">
                <tr>
                  <th
                    class=""
                    style="width: 100%"
                  />
                  <th
                    colspan="2"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider border-l-4 border-gray-200"
                  >
                    {{ __('Patients') }}
                  </th>
                  <th
                    colspan="6"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider border-l-4 border-gray-200"
                  >
                    {{ __('Disposition') }}
                  </th>
                  <th
                    colspan="2"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider border-l-4 border-gray-200"
                  >
                    {{ __('Survival Rate') }}
                  </th>
                </tr>
                <tr>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider align-bottom border"
                  />
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider align-bottom border border-l-4 border-gray-200"
                  >
                    {{ __('Admitted') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider align-bottom border"
                  >
                    {{ __('Species') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider align-bottom border border-l-4 border-gray-200"
                  >
                    {{ __('Pending') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider align-bottom border"
                  >
                    {{ __('Released') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider align-bottom border"
                  >
                    {{ __('Transferred') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider align-bottom border"
                  >
                    {{ __('Died') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider align-bottom border"
                  >
                    {{ __('Euthanized') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider align-bottom border"
                  >
                    {{ __('DOA') }}
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider align-bottom border border-l-4 border-gray-200"
                  >
                    Including<br>First 24hrs
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider align-bottom border"
                  >
                    After<br>First 24hrs
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(row, id) in rows"
                  :key="row.group"
                >
                  <th class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-600 tracking-wider text-gray-600 text-left border">
                    {{ row.group }}
                  </th>
                  <td class="px-1 py-4 whitespace-nowrap text-sm text-gray-500 text-center border border-l-4 border-gray-200">
                    {{ row.total }}
                  </td>
                  <td class="px-1 py-4 whitespace-nowrap text-sm text-gray-500 text-center border">
                    {{ row.species }}
                  </td>
                  <td class="px-1 py-4 whitespace-nowrap text-sm text-gray-500 text-center border border-l-4 border-gray-200">
                    {{ row.pending }}
                  </td>
                  <td class="px-1 py-4 whitespace-nowrap text-sm text-gray-500 text-center border">
                    {{ row.released }}
                  </td>
                  <td class="px-1 py-4 whitespace-nowrap text-sm text-gray-500 text-center border">
                    {{ row.transferred }}
                  </td>
                  <td class="px-1 py-4 whitespace-nowrap text-sm text-gray-500 text-center border">
                    {{ row.died }}
                  </td>
                  <td class="px-1 py-4 whitespace-nowrap text-sm text-gray-500 text-center border">
                    {{ row.euthanized }}
                  </td>
                  <td class="px-1 py-4 whitespace-nowrap text-sm text-gray-500 text-center border">
                    {{ row.doa }}
                  </td>
                  <td class="px-1 py-4 whitespace-nowrap text-sm text-gray-500 text-center border border-l-4 border-gray-200">
                    {{ row.survival_rate_including_24hr }}%
                  </td>
                  <td class="px-1 py-4 whitespace-nowrap text-sm text-gray-500 text-center border">
                    {{ row.survival_rate_after_24hr }}%
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
