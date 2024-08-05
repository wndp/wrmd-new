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

<script>
import Highcharts from 'highcharts';
import { Chart } from 'highcharts-vue';
import Loading from '@/Components/Loading.vue';

export default {
    components: {
        Loading,
        Chart
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
    },
    data () {
        return {
            loading: true,
            // options: {
            //     chart: {
            //         type: 'area',
            //         alignTicks: false,
            //         zoomType: 'x'
            //     },
            //     xAxis: {
            //         categories: [],
            //         crosshair: true,
            //         labels: {
            //             rotation: -45,
            //             align: 'right'
            //         },
            //     },
            //     yAxis: {
            //         endOnTick: false,
            //         title: {
            //             text: ''
            //         }
            //     },
            //     tooltip: {
            //         shared: true,
            //         crosshairs: true
            //     },
            //     plotOptions: {
            //         series: {
            //             label: {
            //                 enabled: false
            //             },
            //             fillOpacity: 0.25
            //         }
            //     },
            // }
        };
    },
    watch: {
        id() {
            this.loading = true;
            this.getData();
        },
        title() {
            this.loading = true;
            this.getData();
        }
    },
    created() {
        this.getData();
        mitt.on('appliedAnalyticsFilters', this.listener);
    },
    beforeUnmount() {
        mitt.off('appliedAnalyticsFilters', this.listener);
    },
    methods: {
        getData() {
            axios.get('/analytics/charts/' + this.id, {
                params: this.params
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

            this.loading = false;
        },
        listener() {
            this.loading = true;
            this.getData();
        }
    }
};
</script>
