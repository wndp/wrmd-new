<script>
import Graph from './Graph.vue';
import Highcharts from './Highcharts';

export default {
    extends: Graph,
    data () {
        return {
            options: {
                series: [{
                    type: 'treemap',
                    layoutAlgorithm: 'squarified',
                    allowDrillToNode: true,
                    animationLimit: 1000,
                    levelIsConstant: false,
                    colorByPoint: true,
                    dataLabels: {
                        enabled: false
                    },
                    data: [],
                    levels: [{
                        level: 1,
                        dataLabels: {
                            enabled: true
                        },
                        borderWidth: 6
                    }, {
                        level: 2,
                        dataLabels: {
                            enabled: true
                        },
                        borderWidth: 4
                    }, {
                        level: 3,
                        dataLabels: {
                            enabled: true
                        },
                        borderWidth: 2
                    }, {
                        level: 6,
                        dataLabels: {
                            enabled: false
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
                xAxis: {
                    categories: data.categories
                }
            }, this.configOptions);

            this.loading = false;
        },
    }
};
</script>
