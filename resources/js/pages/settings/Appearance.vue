<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import { useLanguage } from '@/composables/useLanguage';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

const { language, setLanguage } = useLanguage();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Appearance settings',
        href: '/settings/appearance',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Appearance settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Appearance settings" description="Update your account's appearance settings" />
                <AppearanceTabs />
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <h2 class="font-semibold">Language / Idioma</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Choose the language used throughout the application.</p>
                    <div class="mt-4 flex gap-2">
                        <button
                            type="button"
                            class="rounded-full border px-4 py-2 text-sm font-medium transition"
                            :class="language === 'en' ? 'border-primary bg-primary text-primary-foreground' : 'hover:bg-muted'"
                            @click="setLanguage('en')"
                        >
                            English
                        </button>
                        <button
                            type="button"
                            class="rounded-full border px-4 py-2 text-sm font-medium transition"
                            :class="language === 'pt' ? 'border-primary bg-primary text-primary-foreground' : 'hover:bg-muted'"
                            @click="setLanguage('pt')"
                        >
                            Português
                        </button>
                    </div>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
