<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Activity, AlertTriangle, CheckCircle2, Clock3 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

defineProps<{
    name?: string;
}>();

const stats = [
    { label: 'Active monitors', value: '12', detail: 'All configured targets', icon: Activity, tone: 'text-sky-600' },
    { label: 'Healthy monitors', value: '11', detail: '91.7% of your monitors', icon: CheckCircle2, tone: 'text-emerald-600' },
    { label: 'Open incidents', value: '1', detail: 'Needs your attention', icon: AlertTriangle, tone: 'text-amber-600' },
    { label: 'Avg. response time', value: '184 ms', detail: 'Last 24 hours', icon: Clock3, tone: 'text-violet-600' },
];

const recentChecks = [
    { name: 'Public API', url: 'api.pulsemonitor.test/health', status: 'Operational', latency: '142 ms', checked: '2 min ago' },
    { name: 'Marketing site', url: 'pulsemonitor.test', status: 'Operational', latency: '98 ms', checked: '3 min ago' },
    { name: 'Billing webhook', url: 'api.pulsemonitor.test/webhooks', status: 'Degraded', latency: '812 ms', checked: '4 min ago' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Good morning</h1>
                <p class="text-muted-foreground">Here’s the current health of your services.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div v-for="stat in stats" :key="stat.label" class="rounded-xl border bg-card p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-muted-foreground">{{ stat.label }}</p>
                        <component :is="stat.icon" class="size-5" :class="stat.tone" />
                    </div>
                    <p class="mt-3 text-2xl font-semibold">{{ stat.value }}</p>
                    <p class="mt-1 text-xs text-muted-foreground">{{ stat.detail }}</p>
                </div>
            </div>

            <div class="rounded-xl border bg-card shadow-sm">
                <div class="flex items-center justify-between border-b p-5">
                    <div>
                        <h2 class="font-semibold">Recent checks</h2>
                        <p class="text-sm text-muted-foreground">Latest results from your monitoring targets.</p>
                    </div>
                    <a href="/monitors" class="text-sm font-medium text-primary hover:underline">View monitors</a>
                </div>
                <div class="divide-y">
                    <div v-for="check in recentChecks" :key="check.name" class="grid gap-2 p-5 sm:grid-cols-[1fr_auto_auto] sm:items-center">
                        <div>
                            <p class="font-medium">{{ check.name }}</p>
                            <p class="text-sm text-muted-foreground">{{ check.url }}</p>
                        </div>
                        <span class="text-sm" :class="check.status === 'Operational' ? 'text-emerald-600' : 'text-amber-600'">{{
                            check.status
                        }}</span>
                        <div class="text-left text-sm text-muted-foreground sm:text-right">
                            <p>{{ check.latency }}</p>
                            <p>{{ check.checked }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
