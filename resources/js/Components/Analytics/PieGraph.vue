<script>
import Graph from './Graph.vue';

export default {
    extends: Graph,
    data () {
        return {
            options: {
                chart: {
                    type: 'pie',
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.point.name + '</b><br>Value: ' + this.y + '<br>Percentage: '+ Math.round(this.percentage) +'%';
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f}%'
                        },
                        showInLegend: true
                    }
                }
            }
        };
    }
};
</script>
