<script setup>
import {inject, ref, watch, onMounted, onUnmounted} from 'vue';
import Highcharts from 'highcharts';
import { Chart } from 'highcharts-vue';
import Loading from '@/Components/Loading.vue';
import axios from 'axios';

const emitter = inject('emitter');

const props = defineProps({
    id: String,
    title: String,
    subtitle: {
        type: String,
        required: false,
        default: null
    },
    height: {
        type: Number,
        required: false,
        default: 400
    },
});

const loading = ref(true);

watch(() => [
    props.id,
    props.title
], () => {
    loading.value = true;
    getData();
});

const getData = () => {
    axios.get('/analytics/charts/' + props.id, {
        params: props.params
    })
        .then(response => {
            drawChart(response.data);
        });
};

const drawChart = (data) => {
    this.options = Highcharts.merge(this.options, {
        title: {
            text: props.title
        },
        subtitle: {
            text: props.subtitle
        },
        series: data.series,
        xAxis: {
            categories: data.categories
        },
        plotOptions: {
            series: {
                label: {
                    enabled: data.series.length > 1
                }
            }
        },
        exporting: {
            sourceWidth: 800,
            sourceHeight: 600
        }
    });

    loading.value = false;
};

const listener = () => {
    loading.value = true;
    getData();
};

onMounted(() => {
    getData();
    emitter.on('appliedAnalyticsFilters', listener);
});

onUnmounted(() => emitter.off('appliedAnalyticsFilters', listener));
</script>

<template>
  <div class="bg-white overflow-hidden shadow rounded-b-lg">
    <div class="sm:p-2">
      <Loading
        v-if="loading"
        :style="{height: `${height}px`}"
      />
      <template v-else>
        <Chart
          ref="highcharts"
          :style="{height: `${height}px`}"
          :options="options"
        />
      </template>
    </div>
  </div>
</template>
