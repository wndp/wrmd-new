<script>
import Graph from './Graph.vue';
import Highcharts from './Highcharts';

export default {
    extends: Graph,
    data () {
        let vm = this;
        return {
            options: {
                series: [{
                    type: 'sunburst',
                    data: [],
                    allowDrillToNode: true,
                    cursor: 'pointer',
                    dataLabels: {
                        format: '{point.name}',
                        filter: {
                            property: 'innerArcLength',
                            operator: '>',
                            value: 5
                        },
                        rotationMode: 'circular'
                    },
                    levels: [{
                        level: 1,
                        levelIsConstant: false,
                        dataLabels: {
                            filter: {
                                property: 'outerArcLength',
                                operator: '>',
                                value: 64
                            }
                        }
                    }, {
                        level: 2,
                        colorByPoint: true
                    },
                    {
                        level: 3,
                        colorVariation: {
                            key: 'brightness',
                            to: -0.5
                        }
                    }, {
                        level: 4,
                        colorVariation: {
                            key: 'brightness',
                            to: 0.5
                        }
                    }]
                }]
            }
        };
    },
    methods: {
        drawChart(data) {
            this.options.series[0].data = data.series;
            this.options = Highcharts.merge(this.options, {
                title: {
                    text: this.title
                },
                subtitle: {
                    text: this.subtitle
                },
                // xAxis: {
                //     categories: data.categories
                // }
            }, this.configOptions);

            this.loading = false;
        },
    }
};
</script>
