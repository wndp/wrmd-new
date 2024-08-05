<template>
  <div class="fle items-end bg-white shadow overflow-hidden border-b border-gray-300 sm:rounded-lg relative">
    <Loading
      v-if="loading"
      :style="{height: `${height}px`}"
    />
    <button
      v-else
      type="button"
      class="block w-full"
      @click="$emit('spark-show', title, id, params)"
    >
      <Chart
        ref="highcharts"
        :style="{height: `${height}px`}"
        :options="options"
      />
    </button>
  </div>
</template>

<script>
import Graph from './Graph.vue';

export default {
    extends: Graph,
    props: {
        height: {
            type: Number,
            default: 100
        },
    },
    emits: ['spark-show'],
    data () {
        return {
            options: {
                credits: {
                    enabled: false
                },
                chart: {
                    backgroundColor: null,
                    borderWidth: 0,
                    type: 'area',
                    margin: [25, 0, 0, 0],
                    //width: null,
                    height: null,
                    style: {
                        overflow: 'visible'
                    },
                    skipClone: true
                },
                title: {
                    align: 'left',
                    floating: true,
                    x: -5,
                    y: 5,
                    useHTML: true,
                    widthAdjust: 0,
                    style: {
                        color: 'rgb(23, 23, 23)',
                        font: '400 0.875rem "Open Sans", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
                    }
                },
                xAxis: {
                    visible: false,
                    labels: {
                        enabled: false
                    },
                    title: {
                        text: null
                    },
                    startOnTick: false,
                    endOnTick: false,
                    tickmarkPlacement: 'on',
                    tickPositions: []
                },
                yAxis: {
                    endOnTick: false,
                    startOnTick: false,
                    labels: {
                        enabled: false
                    },
                    title: {
                        text: null
                    },
                    tickPositions: []
                },
                legend: {
                    enabled: false
                },
                tooltip: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        animation: false,
                        lineWidth: 1,
                        shadow: false,
                        pointPlacement: 'on',
                        states: {
                            hover: {
                                lineWidth: 1
                            }
                        },
                        marker: {
                            radius: 1,
                            states: {
                                hover: {
                                    radius: 2
                                }
                            }
                        },
                        label: {
                            enabled: false
                        },
                        fillOpacity: 0.25
                    },
                    column: {
                        negativeColor: '#910000',
                        borderColor: 'silver'
                    }
                },
                exporting: {
                    enabled: false
                }
            }
        };
    }
};
</script>
