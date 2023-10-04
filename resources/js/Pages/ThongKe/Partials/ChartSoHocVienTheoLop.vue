<script setup>
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';
import ChartDataLabels from 'chartjs-plugin-datalabels';

const props = defineProps([]);

const chartElement = ref(null);
let myChart = null;

let colors = ["#f44336", "#e91e63", "#9c27b0", "#673ab7", "#3f51b5", "#2196f3", "#03a9f4", "#00bcd4", "#009688", "#4caf50", "#8bc34a", "#cddc39", "#ffeb3b", "#ffc107", "#ff9800", "#ff5722", "#795548", "#9e9e9e", "#607d8b"];

const makeChartColors = (numberOfColors, opacity = 1) => {
    let opacityColors = [];
    let solidColors = [];

    for (let i = 1; i <= numberOfColors; i++) {
        let color = i <= colors.length ? colors[i - 1] : colors[ i - colors.length - 1 ];
        let opacityCode = `0${Math.round((255 / 100) * opacity).toString(16)}`.slice(-2).toUpperCase();
        solidColors.push(color);
        opacityColors.push(color + opacityCode);
    }

    return {
        solidColors,
        opacityColors
    }
}

function initChart()
{
    axios.get(route('thong-ke.get-so-hoc-vien-theo-lop'))
        .then((response) => {
            let data = response.data;
            drawChart(data);
        })
}

function drawChart(data)
{
    let chartColors = makeChartColors(data.length, 50);
    if (myChart != null) {
        myChart.destroy();
    }
    console.log(chartColors)
    myChart = new Chart(
        chartElement.value,
        {
            plugins: [ChartDataLabels],
            type: 'pie',
            data: {
                labels: data.map(row => `${row.ten}`),
                datasets: [
                    {
                        label: 'Số học viên theo lớp',
                        data: data.map(row => row.hoc_vien_count),
                        backgroundColor: chartColors.opacityColors,
                        hoverOffset: 4
                    },

                ],
            },
        }
    );
}

onMounted(() => {
    initChart();
})

</script>

<template>
    <div>
        <canvas ref="chartElement"></canvas>
    </div>
</template>