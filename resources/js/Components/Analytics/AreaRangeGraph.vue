<script>
import Graph from './Graph.vue';

export default {
    extends: Graph,
    data () {
        return {
            options: {
                chart: {
                    // type: 'area',
                    // alignTicks: false,
                    zoomType: 'x'
                },
                tooltip: {
                    crosshairs: true,
                    shared: true,
                },
                xAxis: {
                    //categories: [],
                    crosshair: true,
                    tickWidth: 0
                    // labels: {
                    //     rotation: -45,
                    //     align: 'right'
                    // },
                },
                yAxis: {
                    min: 0
                    // endOnTick: false,
                    // title: {
                    //     text: ''
                    // }
                },
                // tooltip: {
                //     shared: true,
                //     crosshairs: true
                // },
                // plotOptions: {
                //     series: {
                //         label: {
                //             enabled: false
                //         },
                //         fillOpacity: 0.25
                //     }
                // },
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
