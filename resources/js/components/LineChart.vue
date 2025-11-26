<template>
    <div class="chart-card">
        <div class="chart-header">
            <h3 class="chart-title">{{ data.label }}</h3>
            <div class="chart-legend">
                <div class="legend-item">
                    <span
                        class="legend-color"
                        :style="{ backgroundColor: chartColor }"
                    ></span>
                    <span class="legend-text">Sales</span>
                </div>
            </div>
        </div>

        <div class="chart-container">
            <canvas ref="chartCanvas"></canvas>
        </div>

        <div v-if="loading" class="chart-loading">
            <div class="loading-spinner"></div>
            <p>Loading chart data...</p>
        </div>

        <div v-if="error" class="chart-error">
            <p>Error loading chart: {{ error }}</p>
            <button @click="fetchData" class="retry-btn">Retry</button>
        </div>
    </div>
</template>

<script lang="ts">
import { Chart, registerables } from 'chart.js';

export default {
    name: 'LineChart',
    props: {
        endpoint: {
            type: String,
            required: true,
        },
        color: {
            type: String,
            default: '#3B82F6',
        },
        height: {
            type: String,
            default: '300px',
        },
    },
    data() {
        return {
            data: {
                label: '',
                values: {},
            },
            loading: false,
            error: null,
            chart: null,
        };
    },
    computed: {
        chartColor() {
            return this.color;
        },

        chartData() {
            const labels = Object.keys(this.data.values);
            const values = Object.values(this.data.values);

            return {
                labels: labels.map((date) => this.formatDate(date)),
                datasets: [
                    {
                        label: 'Sales',
                        data: values,
                        borderColor: this.chartColor,
                        backgroundColor: this.hexToRgba(this.chartColor, 0.1),
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: this.chartColor,
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                ],
            };
        },
    },
    mounted() {
        Chart.register(...registerables);
        this.fetchData();
    },
    beforeUnmount() {
        if (this.chart) {
            this.chart.destroy();
        }
    },
    methods: {
        async fetchData() {
            this.loading = true;
            this.error = null;

            try {
                const response = await fetch(this.endpoint);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                this.data = await response.json();

                this.$nextTick(() => {
                    this.initChart();
                });
            } catch (error) {
                console.error('Error fetching chart data:', error);
                this.error = error.message;
            } finally {
                this.loading = false;
            }
        },

        initChart() {
            if (this.chart) {
                this.chart.destroy();
            }

            const ctx = this.$refs.chartCanvas;
            if (!ctx) return;

            this.chart = new Chart(ctx, {
                type: 'line',
                data: this.chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#0a0a0a',
                            bodyColor: '#0a0a0a',
                            borderColor: this.chartColor,
                            borderWidth: 1,
                            callbacks: {
                                label: (context) => {
                                    return `Sales: $${context.parsed.y.toLocaleString()}`;
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)',
                            },
                            ticks: {
                                color: '#9ca3af',
                                maxRotation: 0,
                                callback: (value, index) => {
                                    return index % 7 === 0
                                        ? this.chartData.labels[index]
                                        : '';
                                },
                            },
                            border: {
                                color: 'rgba(255, 255, 255, 0.2)',
                            },
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)',
                            },
                            ticks: {
                                color: '#9ca3af',
                                callback: (value) => {
                                    return value >= 1000
                                        ? `$${value / 1000}k`
                                        : `$${value}`;
                                },
                            },
                            border: {
                                color: 'rgba(255, 255, 255, 0.2)',
                            },
                        },
                    },
                    interaction: {
                        intersect: false,
                        mode: 'nearest',
                    },
                    elements: {
                        line: {
                            tension: 0.4,
                        },
                    },
                },
            });
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
            });
        },

        hexToRgba(hex, alpha) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        },
    },
    watch: {
        endpoint: {
            handler() {
                this.fetchData();
            },
        },
        color() {
            if (this.chart) {
                this.chart.destroy();
                this.initChart();
            }
        },
    },
};
</script>

<style scoped>
.chart-card {
    background: #0a0a0a;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow:
        0 4px 6px -1px rgba(0, 0, 0, 0.4),
        0 2px 4px -1px rgba(0, 0, 0, 0.3);
    height: 100%;
    display: flex;
    flex-direction: column;
    border: 1px solid #1f1f1f;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.chart-title {
    color: #f8fafc;
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0;
}

.chart-legend {
    display: flex;
    align-items: center;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.legend-text {
    color: #9ca3af;
    font-size: 0.875rem;
}

.chart-container {
    position: relative;
    flex: 1;
    min-height: 300px;
}

.chart-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 300px;
    color: #9ca3af;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #262626;
    border-left: 4px solid #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
}

.chart-error {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 300px;
    color: #ef4444;
    text-align: center;
}

.retry-btn {
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    background-color: #3b82f6;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.retry-btn:hover {
    background-color: #2563eb;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
