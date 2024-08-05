<script>
import Graph from './Graph.vue';

export default {
    extends: Graph,
    data () {
        return {
            options: {
                chart: {
                    type: 'bar',
                    zoomType: 'x'
                },
                xAxis: {
                    title: {
                        text: null
                    }
                },
                yAxis: {
                    min: 0,
                    labels: {
                        overflow: 'justify'
                    }
                },
                tooltip: {
                    shared: true
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                }
            }
        };
    }
};
</script>
