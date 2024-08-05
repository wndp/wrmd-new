<script>
import Graph from './Graph.vue';

export default {
    extends: Graph,
    data () {
        return {
            options: {
                chart: {
                    type: 'streamgraph',
                    zoomType: 'x',
                },
                title: {
                    floating: false,
                    align: 'left'
                },
                subtitle: {
                    floating: false,
                    align: 'left'
                },
                xAxis: {
                    maxPadding: 0,
                    crosshair: true,
                    categories: [],
                    lineWidth: 0,
                    margin: 10,
                    tickWidth: 0
                },
                yAxis: {
                    visible: false,
                    startOnTick: false,
                    endOnTick: false
                }
            }
        };
    }
};
</script>
