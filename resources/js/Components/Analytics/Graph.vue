<template>
  <div class="bg-white overflow-hidden shadow rounded-lg">
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
    <div
      v-if="$slots.footing"
      class="px-4 py-2 sm:px-6 bg-gray-50 rounded-b-lg"
    >
      <slot name="footing" />
    </div>
  </div>
</template>

<script>
import { Chart } from 'highcharts-vue';
import Highcharts from './Highcharts';
import Loading from '@/Components/Loading.vue';

export default {
    components: {
        Chart,
        Loading
    },
    props: {
        id: String,
        title: String,
        subtitle: {
            type: String,
            required: false
        },
        height: {
            type: Number,
            required: false,
            default: 400
        },
        urlParams: {
            type: Object,
            default () {
                return {};
            }
        },
        configOptions: {
            type: Object,
            default () {
                return {};
            }
        }
    },
    data () {
        return {
            loading: true,
            options: {
                chart: {
                    //height: null
                },
                title: {
                    text: this.title
                },
                subtitle: {
                    text: this.subtitle
                },
                // xAxis: {
                //     categories: []
                // },
                // yAxis: {
                //     //height: '100%'
                // },
                series: [],
                exporting: {
                    sourceWidth: 800,
                    sourceHeight: 600
                }
            }
        };
    },

    created () {
        this.getData();
        //mitt.on('appliedAnalyticsFilters', this.listener);
    },

    beforeUnmount () {
        //mitt.off('appliedAnalyticsFilters', this.listener);
    },
    methods: {
        getData() {
            window.axios.get('/analytics/charts/' + this.id, {
                params: this.urlParams
            })
                .then(response => {
                    this.drawChart(response.data);
                });
        },
        drawChart(data) {
            this.options = Highcharts.merge(this.options, {
                title: {
                    text: this.title
                },
                subtitle: {
                    text: this.subtitle
                },
                series: data.series,
                xAxis: {
                    categories: data.categories
                }
            }, this.configOptions);

            this.loading = false;
        },
        // listener() {
        //     this.loading = true;
        //     this.getData();
        // }
    }
};
</script>
