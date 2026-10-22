<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { AlertTriangle } from 'lucide-vue-next';

const breadcrumbs = [{ title: 'Incidents', href: '/incidents' }];
</script>

<template>
    <Head title="Incidents" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Incidents</h1>
                <p class="text-muted-foreground">Review outages and recovery history for your services.</p>
            </div>
            <div class="flex min-h-72 flex-col items-center justify-center rounded-xl border border-dashed bg-card p-8 text-center">
                <AlertTriangle class="mb-3 size-8 text-muted-foreground" />
                <h2 class="font-semibold">No incidents to display</h2>
                <p class="mt-1 max-w-md text-sm text-muted-foreground">
                    Incident detection will populate this view after monitoring checks are implemented.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
