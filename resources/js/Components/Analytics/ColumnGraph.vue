<script>
import Graph from './Graph.vue';

export default {
    extends: Graph,
    data () {
        return {
            options: {
                chart: {
                    type: 'column'
                },
                xAxis: {
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    endOnTick: false,
                    title: {
                        text: ''
                    }
                },
                tooltip: {
                    shared: true
                },
            }
        };
    }
};
</script>
