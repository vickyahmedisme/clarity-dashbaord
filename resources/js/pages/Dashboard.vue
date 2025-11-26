<script setup lang="ts">
import LineChart from '@/components/LineChart.vue';
import StatCard from '@/components/StatCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-8 p-8">
            <h2
                class="bg-gradient-to-r from-orange-400 to-pink-500 bg-clip-text text-3xl font-bold text-transparent"
            >
                Key Metrics Dashboard
            </h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <StatCard endpoint="/api/metrics/open-deals" />
                <StatCard endpoint="/api/metrics/monthly-sales" currency="$" />
                <StatCard endpoint="/api/metrics/active-accounts" />
            </div>

            <h2
                class="bg-gradient-to-r from-teal-400 to-blue-500 bg-clip-text text-3xl font-bold text-transparent"
            >
                Sales Performance Trends
            </h2>
            <div class="mt-4">
                <LineChart endpoint="/api/metrics/daily-sales" />
            </div>
        </div>
    </AppLayout>
</template>
