<template>
    <div class="stat-card" :style="cardStyle">
        <div class="stat-content">
            <h3 class="stat-label">{{ data.label }}</h3>
            <p class="stat-value">{{ currency }} {{ formattedValue }}</p>
        </div>
        <div class="stat-icon" :style="iconStyle">
            <slot name="icon">
                <svg
                    width="28"
                    height="28"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                    ></path>
                </svg>
            </slot>
        </div>
    </div>
</template>

<script lang="ts">
export default {
    name: 'StatCard',
    props: {
        endpoint: {
            type: String,
            required: true,
        },
        color: {
            type: String,
            default: null,
        },
        currency: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            data: {
                label: '',
                value: 0,
            },
            loading: false,
            error: null,
        };
    },
    computed: {
        randomColor() {
            if (this.color) return this.color;

            const colors = [
                '#3B82F6',
                '#10B981',
                '#F59E0B',
                '#8B5CF6',
                '#EC4899',
                '#06B6D4',
                '#84CC16',
                '#F97316',
            ];
            return colors[Math.floor(Math.random() * colors.length)];
        },

        cardStyle() {
            return {
                '--primary-color': this.randomColor,
                '--light-color': this.lightenColor(this.randomColor, 0.15),
            };
        },

        iconStyle() {
            return {
                color: this.randomColor,
            };
        },

        formattedValue() {
            if (typeof this.data.value === 'number') {
                return this.data.value.toLocaleString();
            }
            return this.data.value;
        },
    },
    mounted() {
        this.fetchData();
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
            } catch (error: any) {
                console.error('Error fetching stat data:', error);
                this.error = error.message;
            } finally {
                this.loading = false;
            }
        },

        lightenColor(color: string, factor: number) {
            const hex = color.replace('#', '');
            const r = parseInt(hex.substr(0, 2), 16);
            const g = parseInt(hex.substr(2, 2), 16);
            const b = parseInt(hex.substr(4, 2), 16);

            const lightR = Math.round(r + (255 - r) * factor);
            const lightG = Math.round(g + (255 - g) * factor);
            const lightB = Math.round(b + (255 - b) * factor);

            return `rgb(${lightR}, ${lightG}, ${lightB})`;
        },
    },
    watch: {
        endpoint: {
            immediate: false,
            handler() {
                this.fetchData();
            },
        },
    },
};
</script>

<style scoped>
.stat-card {
    background: #0a0a0a;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow:
        0 4px 6px -1px rgba(0, 0, 0, 0.4),
        0 2px 4px -1px rgba(0, 0, 0, 0.3);
    border-left: 4px solid var(--primary-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid #1f1f1f;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow:
        0 10px 15px -3px rgba(0, 0, 0, 0.5),
        0 4px 6px -2px rgba(0, 0, 0, 0.4);
    background: #111111;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), transparent);
    opacity: 0.6;
}

.stat-content {
    flex: 1;
}

.stat-label {
    color: #9ca3af;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-value {
    color: #f8fafc;
    font-size: 1.875rem;
    font-weight: 700;
    margin: 0;
    line-height: 1;
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 1rem;
    flex-shrink: 0;
    backdrop-filter: blur(10px);
}

.stat-icon svg {
    width: 28px;
    height: 28px;
}

.stat-card.loading .stat-value {
    color: transparent;
    background: linear-gradient(90deg, #262626 25%, #404040 50%, #262626 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 4px;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}
</style>
