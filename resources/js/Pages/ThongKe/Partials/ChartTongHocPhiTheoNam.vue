<script setup>
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';
import ChartDataLabels from 'chartjs-plugin-datalabels';

const props = defineProps([]);

const listYears = [2022, 2023];
let chartYear = ref(new Date().getFullYear());

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

function onChartYearChanged()
{
    initChart();
}

function initChart()
{
    axios.get(route('thong-ke.get-tong-hoc-phi'), {
        params: {
            year: chartYear.value
        }
    }).then((response) => {
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

    myChart = new Chart(
        chartElement.value,
        {
            plugins: [ChartDataLabels],
            type: 'bar',
            data: {
                labels: data.map(row => `Tháng ${row.thang}`),
                datasets: [
                    {
                        label: 'Tổng học phí năm ' + chartYear.value,
                        data: data.map(row => row.tong_tien),
                        backgroundColor: chartColors.opacityColors,
                        borderColor: chartColors.solidColors,
                        borderWidth: 1,
                        datalabels: {
                            align: 'end',
                            anchor: 'end'
                        }
                    },

                ],
            },
            options: {
                aspectRatio: 1/1,
                plugins: {
                    datalabels: {
                        formatter: function(value, context) {
                            return (value / (1000 * 1000) ) + "tr";
                        },
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            // Include a dollar sign in the ticks
                            callback: function(value, index, ticks) {
                                return (value / (1000 * 1000) ) + "tr";
                            }
                        }
                    }
                }
            }
        }
    );
}

onMounted(() => {
    initChart();
})

</script>

<template>
    <div>
        <div class="mb-3 flex items-center">
            <label for="">Chọn năm: </label>
            <select class="ml-2 form-select" v-model="chartYear" @change="onChartYearChanged">
                <option v-for="item in listYears" :value="item">{{ item }}</option>
            </select>
        </div>
        <div>
            <canvas ref="chartElement"></canvas>
        </div>
    </div>
</template>