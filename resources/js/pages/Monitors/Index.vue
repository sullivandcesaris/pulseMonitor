<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Activity, Plus } from 'lucide-vue-next';

const breadcrumbs = [{ title: 'Monitors', href: '/monitors' }];
</script>

<template>
    <Head title="Monitors" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Monitors</h1>
                    <p class="text-muted-foreground">Manage the websites and APIs PulseMonitor checks.</p>
                </div>
                <button
                    class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:opacity-90"
                >
                    <Plus class="size-4" /> Add monitor
                </button>
            </div>
            <div class="flex min-h-72 flex-col items-center justify-center rounded-xl border border-dashed bg-card p-8 text-center">
                <Activity class="mb-3 size-8 text-muted-foreground" />
                <h2 class="font-semibold">Your monitors will appear here</h2>
                <p class="mt-1 max-w-md text-sm text-muted-foreground">
                    Monitor CRUD is the next domain step. This page establishes the authenticated navigation and dashboard surface.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
