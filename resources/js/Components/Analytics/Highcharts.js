import Highcharts from 'highcharts';
import boost from 'highcharts/modules/boost';
import heatmap from 'highcharts/modules/heatmap';
import treemap from 'highcharts/modules/treemap';
import sunburst from 'highcharts/modules/sunburst';
import exporting from 'highcharts/modules/exporting';
import exportData from 'highcharts/modules/export-data';
import highchartsMore from 'highcharts/highcharts-more';
import streamgraph from 'highcharts/modules/streamgraph';
import seriesLabel from 'highcharts/modules/series-label';
import accessibility from 'highcharts/modules/accessibility';
import parallelCoordinate from 'highcharts/modules/parallel-coordinates';

boost(Highcharts);
heatmap(Highcharts);
treemap(Highcharts);
sunburst(Highcharts);
exporting(Highcharts);
exportData(Highcharts);
streamgraph(Highcharts);
seriesLabel(Highcharts);
accessibility(Highcharts);
highchartsMore(Highcharts);
parallelCoordinate(Highcharts);

Highcharts.theme = {
    colors: ['#74ABD4', '#AFCD6D', '#E6BB5F', '#F196A5', '#3B87C0', '#8DB13D', '#D79D22', '#EF8999', '#FCD02B'],
    chart: {
        backgroundColor: '#FFFFFF',
    },
    credits: {
        enabled: false
    },
    title: {
        style: {
            color: '#000',
            font: 'bold 16px "Open Sans", "Helvetica Neue", helvetica, arial, sans-serif'
        }
    },
    subtitle: {
        style: {
            color: '#666666',
            font: 'bold 12px "Open Sans", "Helvetica Neue", helvetica, arial, sans-serif'
        }
    },
    legend: {
        itemStyle: {
            font: '9pt "Open Sans", "Helvetica Neue", helvetica, arial, sans-serif',
            color: 'black'
        },
        itemHoverStyle:{
            color: 'gray'
        }
    },
    plotOptions: {
        series: {
            label: {
                minFontSize: 5,
                maxFontSize: 15,
                style: {
                    color: 'rgba(37,65,87,0.85)'
                }
            }
        }
    },
    lang: {
        thousandsSep: ','
    }
};

Highcharts.setOptions(Highcharts.theme);

// Highcharts.setOptions({
//     lang: {
//         thousandsSep: ','
//     }
// });

export default Highcharts;
