<script>
import Graph from './Graph.vue';

export default {
    extends: Graph,
    data () {
        return {
            options: {
                chart: {
                    type: 'area',
                    alignTicks: false,
                    zoomType: 'x'
                },
                xAxis: {
                    categories: [],
                    crosshair: true,
                    labels: {
                        rotation: -45,
                        align: 'right'
                    },
                },
                yAxis: {
                    endOnTick: false,
                    title: {
                        text: ''
                    }
                },
                tooltip: {
                    shared: true,
                    crosshairs: true
                },
                plotOptions: {
                    series: {
                        label: {
                            enabled: false
                        },
                        fillOpacity: 0.25
                    }
                },
            }
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
    }
};
</script>
